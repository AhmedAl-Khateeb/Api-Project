<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\LoginService;

class AuthController extends Controller
{
    public function __construct(private readonly ApiResponse $apiResponse, private readonly LoginService $loginService)
    {
    }

    public function login(LoginRequest $request)
    {
        $user = $this->loginService->login($request->validated());

        return $this->apiResponse->authenticated(
            new LoginResource($user['user'], $user['token'])
        );
    }
}
