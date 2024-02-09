<?php

namespace App\Http\Requests\v1\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'date_naissance' => 'dateNaissance',
            'lieu_naissance' => 'lieuNaissance',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', Rule::unique('employees', 'prenom')],
            'prenom' => ['required', Rule::unique('employees', 'nom')],
            'localite' => ['required'],
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'direction' => ['required'],
            'csp' => ['required', Rule::in(['M', 'C', 'CS'])],
            'date_naissance' => ['required'],
            'lieu_naissance' => ['required'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')],
            'matricule' => ['required', 'string', Rule::unique('employees', 'matricule')]
        ];
    }
}
