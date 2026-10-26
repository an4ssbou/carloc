<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    private function registrationData(): array
    {
        return [
            'first_name' => 'Client',
            'last_name' => 'Synthetic',
            'email' => 'registration@carloc.test',
            'phone' => '0612345678',
            'password' => 'Maintenance-Local-2026!',
            'password_confirmation' => 'Maintenance-Local-2026!',
        ];
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RoleSeeder::class);
        $data = $this->registrationData();

        $response = $this->post('/register', array_merge($data, [
            'role' => 'super_admin', 'has_store' => true, 'is_active' => false,
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('dashboard', absolute: false));
        $user = User::where('email', 'registration@carloc.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id, 'first_name' => 'Client', 'last_name' => 'Synthetic',
            'email' => 'registration@carloc.test', 'phone' => '0612345678',
            'role' => 'client', 'is_active' => true, 'has_store' => false,
        ]);
        $this->assertTrue($user->hasRole('client'));
        $this->assertFalse($user->hasRole('super_admin'));
        $this->assertNull($user->email_verified_at);
        $this->assertNotSame($data['password'], $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('Maintenance-Local-2026!', $user->password));
    }

    public function test_incomplete_registration_or_mismatched_confirmation_creates_no_account(): void
    {
        $missingPhone = $this->registrationData();
        unset($missingPhone['phone']);
        $this->post('/register', $missingPhone)->assertSessionHasErrors('phone');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);

        $mismatched = array_merge($this->registrationData(), ['password_confirmation' => 'Different-Password!']);
        $this->post('/register', $mismatched)->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
