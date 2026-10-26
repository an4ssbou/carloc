<?php

namespace Tests\Independent;

use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationIndependentTest extends TestCase
{
    use RefreshDatabase;

    private function notice(User $user, string $title, int $age, bool $read = false): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(), 'type' => AppNotification::class,
            'data' => ['title' => $title, 'message' => 'Synthetic body', 'url' => 'javascript:alert(1)'],
            'created_at' => now()->subMinutes($age), 'updated_at' => now()->subMinutes($age),
            'read_at' => $read ? now()->subMinute() : null,
        ]);
    }

    public function test_new_read_notice_is_in_latest_five_and_old_unread_does_not_replace_it(): void
    {
        $user = User::factory()->create();
        foreach (range(1, 6) as $age) $this->notice($user, 'QAUnreadAge'.$age, $age);
        $this->notice($user, 'QANewAlreadyRead', 0, true);
        $before = $user->notifications()->orderBy('id')->get(['id', 'read_at'])->toArray();
        $this->actingAs($user)->get('/vehicles')->assertOk()->assertSee('QANewAlreadyRead')
            ->assertSee('QAUnreadAge4')->assertDontSee('QAUnreadAge5')->assertDontSee('QAUnreadAge6');
        $this->assertSame(6, $user->unreadNotifications()->count());
        $this->assertSame($before, $user->notifications()->orderBy('id')->get(['id', 'read_at'])->toArray());
    }

    public function test_switching_accounts_does_not_reuse_previous_preview_or_mark_either_read(): void
    {
        $first = User::factory()->create(); $second = User::factory()->create();
        $this->notice($first, 'QAPrivateFirst', 1); $this->notice($second, 'QAPrivateSecond', 1);
        $this->actingAs($first)->get('/vehicles')->assertOk()->assertSee('QAPrivateFirst')->assertDontSee('QAPrivateSecond');
        $this->actingAs($second)->get('/vehicles')->assertOk()->assertSee('QAPrivateSecond')->assertDontSee('QAPrivateFirst');
        $this->assertSame(1, $first->unreadNotifications()->count());
        $this->assertSame(1, $second->unreadNotifications()->count());
    }

    public function test_full_history_marks_all_own_pages_read_but_retains_other_account_state(): void
    {
        $user = User::factory()->create(); $other = User::factory()->create();
        foreach (range(1, 17) as $age) $this->notice($user, 'QAPaginated'.$age, $age);
        $this->notice($other, 'QAPrivateOutside', 0);
        $this->actingAs($user)->get('/notifications')->assertOk()->assertDontSee('QAPrivateOutside');
        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertSame(17, $user->notifications()->whereNotNull('read_at')->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
        $this->get('/notifications?page=2')->assertOk()->assertSee('QAPaginated17');
        $this->assertSame(17, $user->notifications()->count());
    }
}
