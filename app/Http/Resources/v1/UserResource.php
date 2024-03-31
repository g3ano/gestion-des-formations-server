<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'name' => $this->name,
                'email' => $this->email,
                'createdAt' => strtotime($this->created_at),
            ],
        ];
    }
}
