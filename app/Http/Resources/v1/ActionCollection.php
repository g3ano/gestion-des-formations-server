<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActionCollection extends ResourceCollection
{

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'pagination' => [
                'page' => $default['meta']['current_page'],
                'pages' => $default['meta']['last_page'],
            ]
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->collection,
        ];
    }
}
