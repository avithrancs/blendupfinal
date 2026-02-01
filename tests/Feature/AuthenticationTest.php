<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * #AUTH-H-01: Login - valid
     * Login with correct email/password redirects to home.
     */
    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        
        // Assert redirect to home or intended page (adjust based on RouteServiceProvider::HOME)
        // Usually it's /dashboard or /
        $response->assertRedirect(route('dashboard')); 
    }

    /**
     * #AUTH-H-02: Login - wrong password
     * Login with valid email and wrong password fails.
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * #AUTH-H-03: Login - unknown email
     * Login with email not in DB fails.
     */
    public function test_users_cannot_authenticate_with_unknown_email(): void
    {
        $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * #AUTH-H-04: Login - empty fields
     * Validation errors for empty fields.
     */
    public function test_login_requires_email_and_password(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /**
     * #AUTH-H-05: Login - SQL injection
     * Attempt SQL injection in email field.
     */
    public function test_login_is_safe_from_sql_injection(): void
    {
        $sqlInjectionEmail = "test@example.com' OR 1=1 --";
        
        $this->post('/login', [
            'email' => $sqlInjectionEmail,
            'password' => 'password',
        ]);

        // Should just be treated as an invalid email login attempt
        $this->assertGuest();
    }

    /**
     * #AUTH-H-06: Registration - happy path
     * Register with unique email.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * #AUTH-H-07: Registration - duplicate email
     * Register with existing email fails.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * #AUTH-H-08: Registration - weak password
     * Try '123'. 
     * Laravel default password rule usually requires min:8.
     */
    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'weakpass@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
