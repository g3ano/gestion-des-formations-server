<?php

namespace App\Services;

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
        return response()->json([
            'errors' => $errors,
        ], $status);
    }
}
