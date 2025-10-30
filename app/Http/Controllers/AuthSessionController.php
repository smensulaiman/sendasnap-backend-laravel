<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthSessionController extends Controller
{
    /**
     * Create a web session using a Sanctum personal access token.
     */
    public function tokenLogin(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $plain = $request->string('token');
        $accessToken = PersonalAccessToken::findToken($plain);
        if (! $accessToken) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $user = $accessToken->tokenable;
        Auth::login($user, true);

        return response()->json(['success' => true]);
    }
}
