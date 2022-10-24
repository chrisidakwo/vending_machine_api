<?php

namespace Tests\Feature\Http\Controllers\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\IntegrationTestCase;

class UserControllerTest extends IntegrationTestCase
{
    public function testItValidatesRequestData(): void
    {
        $response1 = $this->postJson(route('users.store'), [
            'username' => 'username',
            'password' => 'password',
            'role' => User::ROLE_SELLER,
        ]);

        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response1->assertJsonValidationErrorFor('password');

        $user = User::factory()->create();

        $response2 = $this->postJson(route('users.store'), [
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

    public function testCreateUser(): void
    {
        $response = $this->postJson(route('users.store'), [
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

    public function testUpdateUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson(route('users.update', [ 'user' => $user->id ]), [
            'username' => 'chrisidakwo',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('username', 'chrisidakwo');
        self::assertTrue(Hash::check('new_password', $user->refresh()->password));
    }

    public function testUserDepositInvalidAmount(): void
    {
        $user = User::factory()->buyer()->create();
        $response = $this->actingAs($user)->postJson(route('users.deposit'), [
            'amount' => 35
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
           'amount' => 'The selected amount is invalid',
        ]);
    }

    public function testDeposit(): void
    {
        $user = User::factory()->buyer()->deposit(10)->create();
        $response = $this->actingAs($user)->postJson(route('users.deposit'), [
            'amount' => 50
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('deposit', 60);
    }

    public function testResetDeposit(): void
    {
        $user = User::factory()->buyer()->deposit(105)->create();

        $response = $this->actingAs($user)->postJson(route('users.deposit.reset'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('deposit', 0);
    }

    public function testDeleteUser(): void
    {
        $user = User::factory()->buyer()->deposit(50)->create();

        $response = $this->actingAs($user)->deleteJson(route('users.delete', [ 'user' => $user->id ]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
