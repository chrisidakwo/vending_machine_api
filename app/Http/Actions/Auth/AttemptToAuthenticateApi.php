<?php

namespace App\Http\Actions\Auth;

use App\Http\Cache\Auth\LoginRateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AttemptToAuthenticateApi
{
    /**
     * The login rate limiter instance.
     */
    protected LoginRateLimiter $limiter;

    /**
     * Create a new controller instance.
     *
     * @param  LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param callable $next
     * @return mixed|void
     * @throws ValidationException
     */
    public function handle(Request $request, callable $next)
    {
        if ($token = Auth::guard()->attempt($request->only('username', 'password'))) {
            JWTAuth::setToken($token);
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request);
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param Request $request
     * @return void
     *
     * @throws ValidationException
     */
    protected function throwFailedAuthenticationException(Request $request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }
}
