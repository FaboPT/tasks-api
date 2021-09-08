<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'The provided credentials are incorrect.',
                'success' => false
            ]);
        }
        $notifications = [];
        if ($user->hasRole('Manager')) {
            $notifications = $user->unreadNotifications;
        }


        return response()->json([
            'access_token' => $user->createToken($request->email)->plainTextToken,
            'token_type' => 'Bearer',
            'success' => true,
            'notifications' => $notifications,
        ]);
    }
}
