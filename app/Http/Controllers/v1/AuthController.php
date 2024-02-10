<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return $this->failure([
                'message' => 'Your Password is incorrect, please try again'
            ], 422);
        }

        $tokens = DB::table('personal_access_tokens')
            ->selectRaw('COUNT("tokenable_id") as tokens_count')
            ->where('tokenable_id', auth()->user()->id)
            ->first();

        if ($tokens->tokens_count !== 0) {
            auth()->user()->tokens()->delete();
        }

        $token = auth()->user()->createToken('token', ["*"], now()->addDays(15))->plainTextToken;

        return $this->success([
            'token' => $token,
            'tokens' => $tokens,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success([
            'message' => 'You have successfully logged out!',
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

        $token = $user->createToken('token', ["*"], now()->addDays(15))->plainTextToken;

        return $this->success([
            'token' => $token,
        ], 201);
    }
}
