<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use JWTAuth;

class IntegrationTestCase extends TestCase
{
    use RefreshDatabase;

    protected array $headers;

    /**
     * @param $user
     *
     * @return $this
     */
    protected function apiAs($user): static
    {
        $this->headers = [
            'Authorization' => 'Bearer '. JWTAuth::fromUser($user),
        ];

        return $this;
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
