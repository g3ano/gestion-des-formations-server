<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomaineResource extends JsonResource
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
            'abbr' => $this->abbr,
            'domaine' => $this->domaine,
            'createdAt' => date('Y-m-d', strtotime($this->created_at)),
        ];
    }
}
