<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

trait HandleHttpResponse
{
    public function success($data = [], $status = 200, $withoutWrapping = false)
    {
        $data = $withoutWrapping
            ? $data
            : ['data' => $data];

        return response()->json($data, $status);
    }

    public function failure(array $errors, $status = 400)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $errors,
            ], $status)
        );
    }
}
