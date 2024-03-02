<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\ActionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'employee' => [
                'id' => $this->id,
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'localite' => $this->localite,
                'sexe' => $this->sexe,
                'direction' => $this->direction,
                'csp' => $this->csp,
                'dateNaissance' => strtotime($this->date_naissance),
                'lieuNaissance' => $this->lieu_naissance,
                'email' => $this->email,
                'matricule' => $this->matricule,
                'createdAt' => strtotime($this->created_at),
            ],
            'relationships' => [
                'actions' => ActionResource::collection(
                    $this->whenLoaded('actions')
                ),
            ],
        ];
    }
}
