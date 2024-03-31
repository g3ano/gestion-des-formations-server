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
                'direction' => $this->direction,
                'sexe' => $this->sexe,
                'dateNaissance' => strtotime($this->date_naissance),
                'lieuNaissance' => $this->lieu_naissance,
                'email' => $this->email,
                'matricule' => $this->matricule,
                'csp' => $this->csp,
                'observation' => $this->whenPivotLoaded(
                    'action_employee',
                    $this->pivot?->observation
                ),
                'isActive' => $this->whenPivotLoaded(
                    'action_employee',
                    $this->isActive ?: false,
                ),
                'startedAt' => $this->whenPivotLoaded(
                    'action_employee',
                    strtotime($this->pivot?->created_at)
                ),
                'createdAt' => strtotime($this->created_at),
            ],
            'relationships' => [
                'actions' => new ActionCollection(
                    $this->whenLoaded('actions')
                ),
            ],
        ];
    }
}
