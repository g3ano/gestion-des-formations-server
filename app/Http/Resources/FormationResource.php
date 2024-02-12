<?php

namespace App\Http\Resources;

use App\Http\Resources\v1\ActionResource;
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
                'intitule' => $this->whenLoaded('intitule'),
                "organisme" => $this->whenLoaded('organisme'),
                "codeDomaine" => $this->whenLoaded('code_domaine'),
                'categorie' => $this->whenLoaded('categorie'),
                "domaine" => $this->whenLoaded('domaine'),
                "type" => $this->whenLoaded('type'),
                'couts' =>  $this->whenLoaded('cout'),
                'actions' =>  ActionResource::collection(
                    $this->whenLoaded('actions')
                ),
            ]
        ];
    }
}
