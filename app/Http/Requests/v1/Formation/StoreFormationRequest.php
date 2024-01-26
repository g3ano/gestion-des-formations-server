<?php

namespace App\Http\Requests\v1\Formation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormationRequest extends FormRequest
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
            'direct.structure' => ['required'],
            'direct.code_formation' => ['required'],
            'direct.mode' => ['required'],
            'direct.lieu' => ['required'],
            'direct.effectif' => ['required'],
            'direct.durree' => ['required'],
            'direct.observation' => ['nullable', 'string', 'max:255'],
            'direct.categorie_id' => ['required', Rule::exists('categories', 'id')],
            'direct.domaine_id' => ['required', Rule::exists('domaines', 'id')],
            'direct.type_id' => ['required', Rule::exists('types', 'id')],
            'common.intitule' => ['required', 'max:255'],
            'common.organisme' => ['required', 'max:55'],
            'common.code_domaine' => ['required'],
            'cout.pedagogiques' => ['required'],
            'cout.hebergement_restauration' => ['required'],
            'cout.transport' => ['required'],
            'cout.presalaire' => ['required'],
            'cout.autres_charges' => ['required'],
            'cout.dont_devise' => ['required'],
        ];
    }
}
