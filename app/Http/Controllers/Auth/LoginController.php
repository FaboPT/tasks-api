<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'The provided credentials are incorrect.',
                'success' => false,
            ]);
        }
        $notifications = [];
        if ($user->hasRole('Manager')) {
            $notifications = $user->unreadNotifications();
        }

        return response()->json([
            'access_token' => $user->createToken($email)->plainTextToken,
            'token_type' => 'Bearer',
            'success' => true,
            'notifications' => $notifications,
        ]);
    }
}
