<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AppNotification;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function notification(User $user, string $title, int $minutesAgo = 0, array $extra = []): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => AppNotification::class,
            'data' => array_merge(['title' => $title, 'message' => 'Message synthétique '.$title, 'url' => '/unused'], $extra),
            'created_at' => now()->subMinutes($minutesAgo),
            'updated_at' => now()->subMinutes($minutesAgo),
        ]);
    }

    public function test_preview_shows_only_latest_five_own_notifications_without_marking_them_read(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        for ($number = 1; $number <= 7; $number++) {
            $this->notification($owner, 'OwnerNotice'.$number, 8 - $number);
        }
        $this->notification($other, 'OtherPrivateNotice');

        $response = $this->actingAs($owner)->get('/vehicles')->assertOk();
        foreach (range(3, 7) as $number) {
            $response->assertSee('OwnerNotice'.$number);
        }
        $response->assertDontSee('OwnerNotice1')->assertDontSee('OwnerNotice2')->assertDontSee('OtherPrivateNotice');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        foreach ($document->getElementsByTagName('a') as $anchor) {
            $this->assertNotSame('/unused', $anchor->getAttribute('href'), 'Les URL stockées dans les notifications ne doivent pas devenir des liens.');
        }
        $this->assertSame(7, $owner->unreadNotifications()->count());
        $this->assertSame(0, $owner->notifications()->whereNotNull('read_at')->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
    }

    public function test_guest_cannot_receive_any_account_notification_preview(): void
    {
        $owner = User::factory()->create();
        $this->notification($owner, 'OwnerPrivateGuestNotice');

        $this->get('/vehicles')->assertOk()->assertDontSee('OwnerPrivateGuestNotice');
        $this->assertSame(1, $owner->unreadNotifications()->count());
    }

    public function test_empty_preview_has_an_empty_state_and_footer_link_to_full_history(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/vehicles')->assertOk();
        $response->assertSee('Aucune notification')->assertSee('Voir tout');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $footerLinks = [];
        foreach ($document->getElementsByTagName('a') as $anchor) {
            if (trim($anchor->textContent) === 'Voir tout') {
                $footerLinks[] = $anchor->getAttribute('href');
            }
        }
        $this->assertNotEmpty($footerLinks, 'Le panneau doit permettre de consulter tout l’historique.');
        foreach ($footerLinks as $href) {
            $this->assertSame(route('notifications.index'), $href);
        }
    }

    public function test_full_history_shows_all_own_notifications_and_marks_only_their_account_read(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        foreach (range(1, 7) as $number) {
            $this->notification($owner, 'HistoryNotice'.$number, $number);
        }
        $this->notification($other, 'OtherHistoryPrivateNotice');

        $response = $this->actingAs($owner)->get('/notifications')->assertOk();
        foreach (range(1, 7) as $number) {
            $response->assertSee('HistoryNotice'.$number);
        }
        $response->assertDontSee('OtherHistoryPrivateNotice');
        $this->assertSame(7, $owner->notifications()->whereNotNull('read_at')->count());
        $this->assertSame(0, $owner->unreadNotifications()->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
    }

    public function test_preview_escapes_untrusted_content_truncates_message_and_ignores_payload_links(): void
    {
        $owner = User::factory()->create();
        $title = '<script>alert("synthetic-title")</script>';
        $message = '<img src=x onerror=alert("synthetic-message")>'.str_repeat('a', 160).'HiddenMessageTail';
        $this->notification($owner, $title, 0, [
            'message' => $message,
            'url' => 'javascript:alert("synthetic-link")',
        ]);

        $response = $this->actingAs($owner)->get('/vehicles')->assertOk();
        $response->assertSee($title)->assertDontSee($title, false)
            ->assertSee('<img src=x onerror=alert("synthetic-message")>')
            ->assertDontSee('<img src=x onerror=alert("synthetic-message")>', false)
            ->assertDontSee('HiddenMessageTail');
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        foreach ($document->getElementsByTagName('a') as $anchor) {
            $this->assertNotSame('javascript:alert("synthetic-link")', $anchor->getAttribute('href'));
        }
        $this->assertSame(1, $owner->unreadNotifications()->count());
    }
}
