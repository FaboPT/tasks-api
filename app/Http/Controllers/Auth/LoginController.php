<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json(['access_token'=>$user->createToken($request->email)->plainTextToken]);
    }
}
