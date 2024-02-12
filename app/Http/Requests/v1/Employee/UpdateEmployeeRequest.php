<?php

namespace App\Http\Requests\v1\Employee;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'date_naissance' => 'date de naissance',
            'lieu_naissance' => 'lieu de naissance',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['bail', 'required', 'max:255', Rule::unique('employees', 'nom')],
            'prenom' => ['bail', 'required', 'max:255', Rule::unique('employees', 'prenom')],
            'sexe' => ['bail', 'required', Rule::in(['M', 'F'])],
            'csp' => ['bail', 'required', Rule::in(['M', 'C', 'CS'])],
            'localite' => ['bail', 'required', 'max:50'],
            'direction' => ['bail', 'required', 'max:50'],
            'date_naissance' => ['bail', 'required', 'string'],
            'lieu_naissance' => ['bail', 'required', 'string', 'max:255'],
            'email' => [
                'bail', 'required', 'email', 'max:255', Rule::unique('employees', 'email')
            ],
            'matricule' => [
                'bail', 'required', 'string', Rule::unique('employees', 'matricule')
            ]
        ];
    }
}
