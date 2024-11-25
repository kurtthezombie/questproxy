<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_create_user_successful()
    {
        $userServiceMock = $this->createMock(UserService::class);

        $user = User::factory()->make();
        $userServiceMock->method('create')->willReturn($user);

        $this->app->instance(UserService::class, $userServiceMock);
    }
}
