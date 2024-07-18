<?php

namespace App\Http\Requests\v1\Formation;

use App\Http\Requests\v1\BaseRequest;
use Illuminate\Validation\Rule;

class StoreFormationRequest extends BaseRequest
{
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
            'direct.observation' => 'observation',
            'common.code_domaine' => 'code domaine',
            'common.intitule' => 'intitule',
            'common.organisme' => 'organisme',
            'direct.effectif' => 'effectif',
            'direct.durree' => 'durree',
            'cout.hebergement_restauration' => 'hébergement restauration',
            'cout.autres_charges' => 'autres charges',
            'cout.dont_devise' => 'dont devise',
            'cout.pedagogiques' => 'pédagogiques',
            'cout.transport' => 'transport',
            'cout.presalaire' => 'présalaire',
        ];
    }

    public function rules(): array
    {
        return [
            'direct.structure' => ['bail', 'required'],
            'direct.code_formation' => ['bail', 'required', 'max:3', Rule::in([
                'CDI', 'CDA', 'CDE', 'LDI', 'LDA', 'LDE',
                'cdi', 'cda', 'cde', 'ldi', 'lda', 'lde'
            ])],
            'direct.mode' => ['bail', 'required'],
            'direct.lieu' => ['bail', 'required'],
            'direct.observation' => ['bail', 'nullable', 'string', 'max:255'],
            'direct.categorie_id' => ['bail', 'required', Rule::exists('categories', 'id')],
            'direct.domaine_id' => ['bail', 'required', Rule::exists('domaines', 'id')],
            'direct.type_id' => ['bail', 'required', Rule::exists('types', 'id')],
            'common.intitule' => ['bail', 'required', 'max:255'],
            'common.organisme' => ['bail', 'required', 'max:55'],
            'common.code_domaine' => ['bail', 'required', 'integer'],
            'direct.effectif' => ['bail', 'required', 'integer'],
            'direct.durree' => ['bail', 'required', 'numeric'],
            'cout.pedagogiques' => ['bail', 'required'],
            'cout.hebergement_restauration' => ['bail', 'required'],
            'cout.transport' => ['bail', 'required'],
            'cout.presalaire' => ['bail', 'required'],
            'cout.autres_charges' => ['bail', 'required'],
            'cout.dont_devise' => ['bail', 'required'],
        ];
    }
}
