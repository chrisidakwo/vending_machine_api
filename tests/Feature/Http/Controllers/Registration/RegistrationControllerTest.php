<?php

namespace Tests\Feature\Http\Controllers\Registration;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\IntegrationTestCase;

class RegistrationControllerTest extends IntegrationTestCase
{
    public function testItValidatesRequestData(): void
    {
        $response1 = $this->postJson(route('register'), [
            'username' => 'username',
            'password' => 'password',
            'role' => User::ROLE_SELLER,
        ]);

        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response1->assertJsonValidationErrorFor('password');

        $user = User::factory()->create();

        $response2 = $this->postJson(route('register'), [
            'username' => $user->username,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_SELLER,
        ]);

        $response2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response2->assertJsonValidationErrors([
            'username' => trans('validation.unique', [
                'attribute' => 'username'
            ]),
        ]);
    }

    public function testRegister(): void
    {
        $response = $this->postJson(route('register'), [
            'username' => 'chrisidakwo',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_SELLER,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'username',
                'role',
                'deposit',
                'createdAt'
            ],
            'meta' => [
                'accessToken',
                'expiresIn'
            ]
        ]);
    }
}
