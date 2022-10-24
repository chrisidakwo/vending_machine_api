<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\DeleteUserRequest;
use App\Http\Requests\Users\DepositRequest;
use App\Http\Requests\Users\ResetDepositRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api', ['except' => ['store']]);

        $this->userService = $userService;
    }

    /**
     * @param StoreUserRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $userData = $request->toArray();

        $user = $this->userService->createUser($userData);

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
        ])->toResponse($request);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function auth(Request $request): JsonResponse
    {
        return response()->json(UserResource::make($request->user()));
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function view(User $user): JsonResponse
    {
        return response()->json(UserResource::make($user));
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->updateUser($user, $request->toArray());

        return response()->json(UserResource::make($user));
    }

    /**
     * @param DepositRequest $request
     *
     * @return JsonResponse
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        $user = $this->userService->deposit($request->user(), $request->get('amount'));

        return response()->json(UserResource::make($user));
    }

    /**
     * @param ResetDepositRequest $request
     *
     * @return JsonResponse
     */
    public function resetDeposit(ResetDepositRequest $request): JsonResponse
    {
        $user = $this->userService->resetDeposit($request->user());

        return response()->json(UserResource::make($user));
    }

    /**
     * @param DeleteUserRequest $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function delete(DeleteUserRequest $request, User $user): JsonResponse
    {
        $result = $this->userService->deleteUser($user);

        /** @var array<bool, int> $statusMap */
        $statusMap = [
            false => Response::HTTP_INTERNAL_SERVER_ERROR,
            true => Response::HTTP_NO_CONTENT,
        ];

        return response()->json([], $statusMap[$result]);
    }
}
