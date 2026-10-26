<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->email_verified_at);

        $response = $this->actingAs($user)->patch('/profile', [
            'first_name' => 'Profile', 'last_name' => 'Synthetic',
            'email' => 'profile@carloc.test', 'phone' => '0712345678',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');
        $user->refresh();
        $this->assertSame('Profile', $user->first_name);
        $this->assertSame('Synthetic', $user->last_name);
        $this->assertSame('profile@carloc.test', $user->email);
        $this->assertSame('0712345678', $user->phone);
        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'first_name' => 'Profile', 'last_name' => 'Synthetic',
            'email' => 'profile@carloc.test', 'phone' => '0712345678', 'email_verified_at' => null,
        ]);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['email_verified_at' => '2026-10-20 09:15:00']);
        $verifiedAt = $user->email_verified_at->toISOString();

        $response = $this->actingAs($user)->patch('/profile', [
            'first_name' => 'Updated', 'last_name' => 'Synthetic',
            'email' => $user->email, 'phone' => $user->phone,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');
        $user->refresh();
        $this->assertSame('Updated', $user->first_name);
        $this->assertSame('Synthetic', $user->last_name);
        $this->assertSame($verifiedAt, $user->email_verified_at->toISOString());
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->actingAs($user)->delete('/profile', ['password' => 'password']);

        $response->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNull(User::find($userId));
        $retained = User::withTrashed()->findOrFail($userId);
        $this->assertTrue($retained->trashed());
        $this->assertSame($user->email, $retained->email);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $userId]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/profile')->delete('/profile', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password')->assertRedirect('/profile');
        $this->assertNotNull($user->fresh());
        $this->assertNull($user->fresh()->deleted_at);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_profile_input_preserves_account_and_verification(): void
    {
        $user = User::factory()->create();
        $before = $user->only(['first_name', 'last_name', 'email', 'phone']);
        $verifiedAt = $user->email_verified_at->toISOString();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'first_name' => 'Should Not Persist', 'last_name' => 'Synthetic',
            'email' => 'not-an-email', 'phone' => 'invalid-phone',
        ])->assertSessionHasErrors(['email', 'phone'])->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame($before, $user->only(['first_name', 'last_name', 'email', 'phone']));
        $this->assertSame($verifiedAt, $user->email_verified_at->toISOString());
        $this->assertAuthenticatedAs($user);
    }
}
