<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'action' => [
                "id" => $this->id,
                "dateDebut" => $this->date_debut,
                "dateFin" => $this->date_fin,
                "observation" => $this->observation ? $this->observation : '',
                "createdAt" => date('Y-m-d', strtotime($this->created_at)),
            ],
            'relationships' => [
                'formation' => FormationResource::make(
                    $this->whenLoaded('formation')
                ),
                'employees' => EmployeeResource::collection(
                    $this->whenLoaded('employees')
                ),
            ],
        ];
    }
}
