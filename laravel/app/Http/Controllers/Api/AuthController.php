<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();
        if ($user === null || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Email atau password tidak sesuai.'], 401);
        }

        $secret = config('jwt.secret');
        if (! is_string($secret) || $secret === '') {
            throw new RuntimeException('JWT_SECRET is not configured.');
        }

        $now = time();
        $ttl = max(60, (int) config('jwt.ttl', 3600));
        $token = JWT::encode([
            'iss' => config('jwt.issuer'),
            'iat' => $now,
            'exp' => $now + $ttl,
            'sub' => (string) $user->getKey(),
        ], $secret, 'HS256');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}
