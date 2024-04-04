<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
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

    public function toArray(Request $request): array
    {
        return [
            ...$this->collection,
        ];
    }
}
