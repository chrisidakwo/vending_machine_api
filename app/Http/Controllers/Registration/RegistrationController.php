<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\UserRegistrationRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Registration\UserRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;

class RegistrationController extends Controller
{
    protected UserRegistrationService $registrationService;

    /**
     * @param UserRegistrationService $registrationService
     */
    public function __construct(UserRegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * @param UserRegistrationRequest $request
     *
     * @return UserResource
     */
    public function register(UserRegistrationRequest $request): UserResource
    {
        $userData = $request->toArray();

        $user = $this->registrationService->register($userData);

        // Login user
        $token = Auth::attempt([
            'username' => $userData['username'],
            'password' => $userData['password'],
        ]);

        return UserResource::make($user)->additional([
            'meta' => [
                'accessToken' => $token,
                'expiresIn' => JWTFactory::getTTL() * 60
            ],
        ]);
    }
}
