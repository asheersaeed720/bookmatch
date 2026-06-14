<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new end user and issue an API token.
     *
     * Keeps the dual role system in sync (per CLAUDE.md): sets the `role` enum
     * column AND assigns the matching Spatie role.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->string('name'),
            'email'    => $request->string('email'),
            'password' => Hash::make((string) $request->string('password')),
            'role'     => UserRole::Student,
        ]);

        $user->assignRole(UserRole::Student->value);

        event(new Registered($user));

        $token = $user->createToken('mobile')->plainTextToken;

        return (new UserResource($user))
            ->additional(['token' => $token])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Authenticate credentials and issue an API token.
     */
    public function login(LoginRequest $request): JsonResource
    {
        $user = $request->authenticate();

        $token = $user->createToken('mobile')->plainTextToken;

        return (new UserResource($user))->additional(['token' => $token]);
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Return the authenticated user.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
