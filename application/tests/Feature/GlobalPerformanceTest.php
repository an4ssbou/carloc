<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class GlobalPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function notices(User $user, int $count): void
    {
        foreach (range(1, $count) as $number) {
            $user->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => AppNotification::class,
                'data' => ['title' => 'Synthetic '.$number, 'message' => 'Synthetic performance fixture', 'url' => '/unused'],
                'created_at' => now()->subMinutes($number),
            ]);
        }
    }

    public function test_history_hydration_is_bounded_and_unread_is_marked_in_one_scoped_write(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->notices($owner, 45);
        $this->notices($other, 2);
        $hydrated = 0;
        $writes = 0;
        DatabaseNotification::retrieved(function () use (&$hydrated) {
            $hydrated++;
        });
        DB::listen(function (QueryExecuted $query) use (&$writes) {
            if (preg_match('/^update\s+[`"]?notifications/i', $query->sql)) {
                $writes++;
            }
        });
        $response = $this->actingAs($owner)->get('/notifications')->assertOk();
        $response->assertViewHas('notifications', fn ($page) => $page->count() === 15 && $page->total() === 45);
        $this->assertLessThanOrEqual(30, $hydrated, 'The request must not hydrate the entire notification history.');
        $this->assertSame(1, $writes);
        $this->assertSame(0, $owner->unreadNotifications()->count());
        $this->assertSame(2, $other->unreadNotifications()->count());
        fwrite(STDOUT, "PERF notification_history hydrated={$hydrated} for 45 records; read_updates={$writes}\n");
    }

    public function test_mark_one_notification_is_scoped_and_idempotent(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $this->notices($owner, 2);
        $this->notices($other, 1);
        $ownId = $owner->notifications()->firstOrFail()->id;
        $otherId = $other->notifications()->firstOrFail()->id;
        $this->actingAs($owner)->post('/notifications/'.$otherId.'/mark-as-read')->assertNotFound();
        $this->assertSame(1, $other->unreadNotifications()->count());
        $this->assertSame(2, $owner->unreadNotifications()->count());
        $this->post('/notifications/'.$ownId.'/mark-as-read')->assertRedirect();
        $firstRead = $owner->notifications()->findOrFail($ownId)->read_at;
        $this->assertNotNull($firstRead);
        $this->assertSame(1, $owner->unreadNotifications()->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
        $this->travel(1)->minutes();
        $this->post('/notifications/'.$ownId.'/mark-as-read')->assertRedirect();
        $this->assertTrue($firstRead->equalTo($owner->notifications()->findOrFail($ownId)->read_at));
        $this->assertSame(1, $owner->unreadNotifications()->count());
    }
}
