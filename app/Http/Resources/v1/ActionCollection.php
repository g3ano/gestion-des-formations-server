<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActionCollection extends ResourceCollection
{

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'meta' => [
                'total' => $default['meta']['total'],
                'perPage' => $default['meta']['per_page'],
                'to' => $default['meta']['to'],
                'from' => $default['meta']['from'],
                'currentPage' => $default['meta']['current_page'],
                'lastPage' => $default['meta']['last_page'],
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
