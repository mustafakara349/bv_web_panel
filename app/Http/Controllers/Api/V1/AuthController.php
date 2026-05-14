<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->apiLogin($request->only('email', 'password'));

        return $this->success([
            'user' => new UserResource($result['user']->load('role')),
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load('role')),
            'Profile fetched successfully'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->apiLogout($request->user());

        return $this->success(null, 'Logged out successfully');
    }
}
