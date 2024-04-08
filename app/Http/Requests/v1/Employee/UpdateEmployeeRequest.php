<?php

namespace App\Http\Requests\v1\Employee;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends BaseRequest
{
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

    public function rules(): array
    {
        return [
            'matricule' => [
                'bail', 'required', 'max:6', 'min:6', 'string',
                Rule::unique('employees', 'matricule')->ignore($this->id),
            ],
            'direction' => ['bail', 'required', 'string', 'max:50'],
            'localite' => ['bail', 'required', 'string', 'max:50'],
            'nom' => ['bail', 'required', 'string', 'max:255'],
            'prenom' => ['bail', 'required', 'string', 'max:255'],
            'sexe' => ['bail', 'required', Rule::in(['M', 'F'])],
            'csp' => ['bail', 'required', Rule::in(['M', 'C', 'CS'])],
            'email' => [
                'bail', 'required', 'email', 'max:255',
                Rule::unique('employees', 'email')->ignore($this->id)
            ],
            'date_naissance' => ['bail', 'required', 'integer'],
            'lieu_naissance' => ['bail', 'required', 'string', 'max:255'],
        ];
    }
}
