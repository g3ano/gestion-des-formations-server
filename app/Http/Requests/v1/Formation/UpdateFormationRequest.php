<?php

namespace App\Http\Requests\v1\Formation;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateFormationRequest extends BaseRequest
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
            'direct.categorie_id' => 'formation categorie',
            'direct.type_id' => 'formation type',
            'direct.domaine_id' => 'domaine',
            'direct.code_formation' => 'code formation',
            'direct.structure' => 'structure',
            'direct.mode' => 'mode',
            'direct.lieu' => 'lieu',
            'direct.effectif' => 'effectif',
            'direct.durree' => 'durree',
            'direct.observation' => 'observation',
            'common.code_domaine' => 'code domaine',
            'common.intitule' => 'intitule',
            'common.organisme' => 'organisme',
            'cout.pedagogiques' => 'pédagogiques',
            'cout.transport' => 'transport',
            'cout.presalaire' => 'présalaire',
            'cout.hebergement_restauration' => 'hébergement restauration',
            'cout.autres_charges' => 'autres charges',
            'cout.dont_devise' => 'dont devise',
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
            'direct.code_formation' => [
                'bail', 'required', 'max:3', Rule::in([
                    'CDI', 'CDA', 'CDE', 'LDI', 'LDA', 'LDE',
                    'cdi', 'cda', 'cde', 'ldi', 'lda', 'lde'
                ])
            ],
            'direct.structure' => ['bail', 'required'],
            'direct.mode' => ['bail', 'required'],
            'direct.lieu' => ['bail', 'required'],
            'direct.effectif' => ['bail', 'required', 'integer'],
            'direct.durree' => ['bail', 'required', 'numeric'],
            'direct.observation' => ['bail', 'nullable', 'string', 'max:255'],
            'direct.categorie_id' => ['bail', 'required', Rule::exists('categories', 'id')],
            'direct.domaine_id' => ['bail', 'required', Rule::exists('domaines', 'id')],
            'direct.type_id' => ['bail', 'required', Rule::exists('types', 'id')],
            'common.intitule' => ['bail', 'required', 'max:255'],
            'common.organisme' => ['bail', 'required', 'max:55'],
            'common.code_domaine' => ['bail', 'required', 'integer'],
            'cout.pedagogiques' => ['bail', 'required'],
            'cout.hebergement_restauration' => ['bail', 'required'],
            'cout.transport' => ['bail', 'required'],
            'cout.presalaire' => ['bail', 'required'],
            'cout.autres_charges' => ['bail', 'required'],
            'cout.dont_devise' => ['bail', 'required'],
        ];
    }
}
