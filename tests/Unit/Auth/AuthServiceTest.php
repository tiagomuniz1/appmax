<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_a_user_and_creates_wallet()
    {
        $service = app(AuthService::class);

        $user = $service->register(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123'
        );

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $this->assertNotNull($user->wallet);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 0,
        ]);
    }

    public function test_does_not_allow_register_with_duplicate_email()
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $service = app(AuthService::class);

        $this->expectException(ValidationException::class);

        $service->register(
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123'
        );
    }

    public function test_logs_in_user_with_valid_credentials()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $service = app(AuthService::class);

        $response = $service->login(
            email: $user->email,
            password: $password
        );

        $this->assertIsArray($response);

        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('token_type', $response);
        $this->assertArrayHasKey('expires_in', $response);

        $this->assertEquals('bearer', $response['token_type']);
        $this->assertNotEmpty($response['access_token']);
    }

    public function test_does_not_log_in_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $service = app(AuthService::class);

        $this->expectException(ValidationException::class);

        $service->login(
            email: $user->email,
            password: 'wrong-password'
        );
    }
}
