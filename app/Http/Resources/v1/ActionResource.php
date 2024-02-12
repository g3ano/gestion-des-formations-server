<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\FormationResource;
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
                "date_debut" => $this->date_debut,
                "date_fin" => $this->date_fin,
                "observation" => $this->observation ? $this->observation : '',
                "created_at" => date('Y-m-d', strtotime($this->created_at)),
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
