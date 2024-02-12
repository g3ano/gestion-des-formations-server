<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoutResource extends JsonResource
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
            'pedagogiques' => $this->pedagogiques,
            'hebergementRestauration' => $this->hebergement_restauration,
            'transport' => $this->transport,
            'presalaire' => $this->presalaire,
            'autresCharges' => $this->autres_charges,
            'dontDevise' => $this->dont_devise,
            'createdAt' => date('Y-m-d', strtotime($this->created_at)),
        ];
    }
}
