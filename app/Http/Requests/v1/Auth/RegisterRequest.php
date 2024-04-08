<?php

namespace App\Http\Requests\v1\Auth;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'max:55', Rule::unique('users', 'name')],
            'email' => ['bail', 'required', 'max:55', 'email', Rule::unique('users', 'email')],
            'password' => ['bail', 'required', 'confirmed', Password::defaults()],
        ];
    }
}
