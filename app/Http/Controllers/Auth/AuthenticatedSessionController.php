<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    //
    public function store(LoginRequest $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in using the validated credentials
        if (Auth::attempt($credentials)) {
            // Get the authenticated user
            $user = Auth::user();
            // Create a new API token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            // Return a response with the token and user ID
            return response()->json(['token' => $token, 'user_id' => $user->id]);
        }

        // Return a response for invalid credentials
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        // For Sanctum
        $request->user()->currentAccessToken()->delete();

        // For Passport, you might use:
        // $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
