<?php

namespace App\Http\Requests\v1\Auth;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'max:55', Rule::unique('users', 'name')],
            'email' => ['bail', 'required', 'max:55', 'email', Rule::unique('users', 'email')],
            'password' => ['bail', 'required', 'confirmed', Password::defaults()],
        ];
    }
}
