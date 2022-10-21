<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use JWTAuth;

class IntegrationTestCase extends TestCase
{
    use RefreshDatabase, InteractsWithAuthentication, InteractsWithDatabase;

    protected array $headers;

    /**
     * @param UserContract $user
     * @param string $guard
     *
     * @return $this
     */
    public function actingAs(UserContract $user, $guard = 'api'): static
    {
        $this->headers = [
            'Authorization' => 'Bearer '. JWTAuth::fromUser($user),
        ];

        return parent::actingAs($user, $guard);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = []): TestResponse
    {
        $response = parent::json($method, $uri, $data, array_merge($headers, $this->headers ?? []));

        $this->headers = [];

        return $response;
    }
}
