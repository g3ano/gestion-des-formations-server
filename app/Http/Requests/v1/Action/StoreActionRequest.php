<?php

namespace App\Http\Requests\v1\Action;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class StoreActionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'action.formation_id' => 'formation',
            'action.date_debut' => 'date debut',
            'action.date_fin' => 'date fin',
            'participants.*.employee_id' => 'employee',
            'participants.*.observation' => 'observation',

        ];
    }

    public function rules(): array
    {
        return [
            'action.formation_id' => ['bail', 'required', Rule::exists('formations', 'id')],
            'action.date_debut' => ['bail', 'required', 'integer'],
            'action.date_fin' => ['bail', 'required', 'integer'],
            'action.prevision' => ['bail', 'nullable', 'max:255'],
            'participants.*.employee_id' => ['bail', 'required', Rule::exists('employees', 'id')],
            'participants.*.observation' => ['bail', 'nullable', 'max:255'],
        ];
    }
}
