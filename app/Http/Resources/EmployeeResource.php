<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'localite' => $this->localite,
            'sexe' => $this->sexe,
            'direction' => $this->direction,
            'csp' => $this->csp,
            'date_naissance' => date('Y-m-d', strtotime($this->date_naissance)),
            'lieu_naissance' => $this->lieu_naissance,
            'email' => $this->email,
            'matricule' => $this->matricule,
            'created_at' => date('Y-m-d', strtotime($this->created_at)),
        ];
    }
}
