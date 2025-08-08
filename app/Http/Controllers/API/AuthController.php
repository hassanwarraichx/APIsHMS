<?php

namespace App\Http\Controllers\API;

use App\DTOs\Auth\RegisterDTO;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $dto = new RegisterDto($request->validated());
        $user = $this->authService->register($dto);
        return ResponseHelper::success(new UserResource($user), 'User registered successfully', 200);

    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('api')->user()),
        ], 'Login successful');
    }


    public function profile()
    {
        return ResponseHelper::success(new UserResource(auth()->user()));
    }

    public function logout()
    {
        auth()->logout();
        return ResponseHelper::success(null, 'Logged out successfully');
    }


}

