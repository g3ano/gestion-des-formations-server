<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            $this->failure([
                'message' => 'Your Password is incorrect, please try again'
            ], 422);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();

        if ($user->tokens->count()) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('token', ["*"], now()->addDays(15))->plainTextToken;

        return $this->success([
            'message' => 'Signed in with success',
            'token' => $token,
        ]);
    }

    public function logout()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $status = $user->tokens()->delete();

        if (!$status) {
            $this->failure([
                'message' => 'Logout failed',
            ], 500);
        }

        return $this->success([
            'message' => 'Logged out',
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        if (!$user) {
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ], 500);
        }

        $token = $user->createToken('token', ["*"], now()->addDays(15))
            ->plainTextToken;

        return $this->success([
            'message' => 'Registered with success',
            'token' => $token,
        ], 201);
    }

    public function user()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        return $this->success(new UserResource($user));
    }
}
