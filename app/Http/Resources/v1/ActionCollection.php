<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActionCollection extends ResourceCollection
{

    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'currentPage' => $default['meta']['current_page'],
            'lastPage' => $default['meta']['last_page'],
            'perPage' => $default['meta']['per_page'],
            'total' => $default['meta']['total'],
            'from' => $default['meta']['from'],
            'to' => $default['meta']['to'],
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
