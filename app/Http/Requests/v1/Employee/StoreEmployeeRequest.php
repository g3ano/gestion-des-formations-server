<?php

namespace App\Http\Requests\v1\Employee;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'date_naissance' => 'date naissance',
            'lieu_naissance' => 'lieu naissance',
        ];
    }

    public function rules(): array
    {
        return [
            'matricule' => [
                'bail', 'required', 'string', 'max:6', 'min:6',
                Rule::unique('employees', 'matricule')
            ],
            'direction' => ['bail', 'required', 'max:50'],
            'localite' => ['bail', 'required', 'max:50'],
            'nom' => ['bail', 'required', 'max:255'],
            'prenom' => ['bail', 'required', 'max:255'],
            'sexe' => ['bail', 'required', Rule::in(['M', 'F'])],
            'csp' => ['bail', 'required', Rule::in(['M', 'C', 'CS'])],
            'email' => [
                'bail', 'required', 'email', 'max:255', Rule::unique('employees', 'email')
            ],
            'date_naissance' => ['bail', 'required', 'integer'],
            'lieu_naissance' => ['bail', 'required', 'max:255'],
        ];
    }
}
