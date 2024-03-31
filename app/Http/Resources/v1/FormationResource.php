<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\ActionResource;
use App\Http\Resources\v1\CoutResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
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
                "createdAt" => strtotime($this->created_at),
            ],
            'relationships' => [
                'intitule' => IntituleResource::make($this->whenLoaded('intitule')),
                "organisme" => OrganismeResource::make($this->whenLoaded('organisme')),
                "codeDomaine" => CodeDomaineResource::make($this->whenLoaded('code_domaine')),
                'categorie' => CategorieResource::make($this->whenLoaded('categorie')),
                "domaine" => DomaineResource::make($this->whenLoaded('domaine')),
                "type" => TypeResource::make($this->whenLoaded('type')),
                'cout' =>  CoutResource::make($this->whenLoaded('cout')),
                'actions' =>  ActionResource::collection(
                    $this->whenLoaded('actions')
                ),
            ]
        ];
    }
}
