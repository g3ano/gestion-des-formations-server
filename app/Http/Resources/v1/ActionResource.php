<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'action' => [
                'id' => $this->id,
                'dateDebut' => strtotime($this->date_debut),
                'dateFin' => strtotime($this->date_fin),
                'prevision' => $this->prevision ? $this->prevision : '',
                'createdAt' => strtotime($this->created_at),
                'activeEmployees' => $this->whenNotNull($this->activeEmployees),
            ],
            'relationships' => [
                'formation' => new FormationResource(
                    $this->whenLoaded('formation')
                ),
                'employees' => EmployeeResource::collection(
                    $this->whenLoaded('employees')
                ),
            ],
        ];
    }
}
