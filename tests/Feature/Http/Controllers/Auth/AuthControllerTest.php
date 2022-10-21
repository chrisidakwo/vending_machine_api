<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\IntegrationTestCase;

class AuthControllerTest extends IntegrationTestCase
{
    public function testLoginRequiresUsernameAndPassword(): void
    {
        $response = $this->postJson(route('auth.login'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('username');
        $response->assertJsonValidationErrorFor('password');
    }

    public function testLoginRequiresValidUsernameAndPassword(): void
    {
        User::factory()->create();

        $response = $this->postJson(route('auth.login'), [
            'username' => 'username',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'username' => trans('auth.failed'),
        ]);
    }

    public function testLogin()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.login'), [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'accessToken',
            'expiresIn',
        ]);
    }

    public function testLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('auth.logout'));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRefreshToken(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('auth.refresh'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'accessToken',
            'expiresIn',
        ]);
    }
}
