<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_created_successfully()
    {
        $userService = new UserService(new User());

        // Data for creating a user
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'f_name' => 'Test',
            'l_name' => 'User',
            'contact_number' => '1234567890',
            'role' => 'gamer',
        ];

        // Act: Call the create method
        $createdUser = $userService->create($data);

        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertTrue(Hash::check($data['password'], $createdUser->password));
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
        $this->assertEmpty($createdUser->email_verified_at);
    }

    public function test_user_is_duplicate(){
        User::factory()->create(['username' => 'test_user']);

        $response = $this->postJson('/api/signup',[
            'username' => 'test_user', // Duplicate username
            'email' => 'user2@example.com', // Unique email
            'password' => 'password123',
            'f_name' => 'Jane',
            'l_name' => 'Smith',
            'contact_number' => '0987654321',
            'role' => 'gamer',
        ]);

        // Assert the API returns a validation error
        $response->assertStatus(422);
    }
}
