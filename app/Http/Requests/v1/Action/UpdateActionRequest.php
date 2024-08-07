<?php

namespace App\Http\Requests\v1\Action;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateActionRequest extends BaseRequest
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
            'action.formation_id' => 'formation',
            'action.date_debut' => 'date debut',
            'action.date_fin' => 'date fin',
            'participants' => 'list des participants',
            'participants.*.employee_id' => 'employee',
            'participants.*.observation' => 'observation',
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
            'action.date_debut' => ['bail', 'required'],
            'action.date_fin' => ['bail', 'required'],
            'action.prevision' => ['bail', 'nullable', 'max:255'],
            'action.formation_id' => ['bail', 'required', Rule::exists('formations', 'id')],
            'participants' => ['bail', 'required', 'array'],
            'participants.*.employee_id' => ['bail', 'required', Rule::exists('employees', 'id')],
            'participants.*.observation' => ['bail', 'nullable', 'max:255'],
        ];
    }

    protected function passedValidation()
    {
        $validated = $this->validated();

        $result = [];
        foreach ($validated['participants'] as $value) {
            $result[$value['employee_id']] = [
                'observation' => $value['observation'],
            ];
        }

        $this->merge([
            'action' => $validated['action'],
            'participants' => $result,
        ]);
    }
}
