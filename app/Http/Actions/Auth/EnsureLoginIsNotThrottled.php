<?php

namespace App\Http\Actions\Auth;


use App\Http\Cache\Auth\LoginRateLimiter;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnsureLoginIsNotThrottled
{
    /**
     * The login rate limiter instance.
     */
    protected LoginRateLimiter $limiter;

    /**
     * Create a new class instance.
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
     * @return mixed
     * @throws ValidationException
     */
    public function handle(Request $request, callable $next): mixed
    {
        if (! $this->limiter->tooManyAttempts($request)) {
            return $next($request);
        }

        event(new Lockout($request));

        return with($this->limiter->availableIn($request), function ($seconds) {
            throw ValidationException::withMessages([
                'username' => [
                    trans('auth.throttle', [
                        'seconds' => $seconds,
                    ]),
                ],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        });
    }
}
