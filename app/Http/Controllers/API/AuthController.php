<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', 401);
        }

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => new UserResource(auth('api')->user()),
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

