<?php

namespace App\Http\Controllers\Auth;

use App\Http\Actions\Auth\AttemptToAuthenticateApi;
use App\Http\Actions\Auth\EnsureLoginIsNotThrottled;
use App\Http\Cache\Auth\LoginRateLimiter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:api')->only('login');
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = (new Pipeline($this->getContainer()))->send($request)->through([
            EnsureLoginIsNotThrottled::class,
            AttemptToAuthenticateApi::class,
        ])->then(function (Request $request) {
            // Clear login rate limiter
            $this->getContainer()->make(LoginRateLimiter::class)->clear($request);

            // Generate from user
            return JWTAuth::getToken()->get();
        });

        return response()->json($this->getTokenResponse($token));
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return response()->json($this->getTokenResponse(Auth::refresh()));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getTokenResponse(string $token): array
    {
        return [
            'accessToken' => $token,
            'expiresIn' => JWTFactory::getTTL() * 60
        ];
    }
}
