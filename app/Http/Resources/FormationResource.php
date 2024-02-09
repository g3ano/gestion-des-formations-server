<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
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
                "codeFormation" => $this->code_formation,
                "mode" => $this->mode,
                "lieu" => $this->lieu,
                "effectif" => $this->effectif,
                "durree" => $this->durree,
                "HJ" => $this->h_j,
                "observation" => $this->observation ? $this->observation : '',
                "createdAt" => date('Y-m-d', strtotime($this->created_at)),
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
                'codeDomaine' => [
                    "id" => $this->code_domaine_id,
                    "codeDomaine" => $this->code_domaine,
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
                    "hebergementRestauration" => $this->hebergement_restauration,
                    "transport" => $this->transport,
                    "presalaire" => $this->presalaire,
                    "autresCharges" => $this->autres_charges,
                    "dontDevise" => $this->dont_devise,
                ],
            ]
        ];
    }
}
