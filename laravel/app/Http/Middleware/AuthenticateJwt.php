<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use DomainException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class AuthenticateJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('jwt.secret');
        if (! is_string($secret) || $secret === '') {
            throw new RuntimeException('JWT_SECRET is not configured.');
        }

        $token = $request->bearerToken();
        if ($token === null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $claims = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (ExpiredException|UnexpectedValueException|DomainException) {
            return response()->json(['message' => 'Token is invalid or expired.'], 401);
        }

        if (($claims->iss ?? null) !== config('jwt.issuer')
            || ! isset($claims->sub)
            || ! ctype_digit((string) $claims->sub)) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        }

        $user = User::find((int) $claims->sub);
        if ($user === null) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        }

        $request->setUserResolver(static fn (): User => $user);

        return $next($request);
    }
}
