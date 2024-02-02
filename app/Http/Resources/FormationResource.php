<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{

    public function with(Request $request)
    {
        return ['resolve' => []];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'formation' => [
                "id" => $this->id,
                "structure" => $this->structure,
                "code_formation" => $this->code_formation,
                "mode" => $this->mode,
                "lieu" => $this->lieu,
                "effectif" => $this->effectif,
                "durree" => $this->durree,
                "h_j" => $this->h_j,
                "observation" => $this->observation ? $this->observation : '',
                "created_at" => date('Y-m-d', strtotime($this->created_at)),
            ],
            'relationships' => [
                'intitule' => [
                    "id" => $this->intitule_id,
                    "intitule" => $this->intitule,
                ],
                'organisme' => [
                    "id" => $this->organisme_id,
                    "organisme" => $this->organisme,
                ],
                'code_domaine' => [
                    "id" => $this->code_domaine_id,
                    "code_domaine" => $this->code_domaine,
                ],
                "categorie" => [
                    'id' => $this->categorie_id,
                    'categorie' => $this->categorie,
                ],
                "domaine" => [
                    'id' => $this->domaine_id,
                    "domaine" => $this->domaine,
                ],
                'type' => [
                    "id" => $this->type_id,
                    "type" => $this->type,
                ],
                'couts' =>  [
                    "id" => $this->cout_id,
                    "pedagogiques" => $this->pedagogiques,
                    "hebergement_restauration" => $this->hebergement_restauration,
                    "transport" => $this->transport,
                    "presalaire" => $this->presalaire,
                    "autres_charges" => $this->autres_charges,
                    "dont_devise" => $this->dont_devise,
                ],
            ]
        ];
    }
}
