<?php

namespace App\Services\Traits;

trait HttpResponseTrait
{
    public function success($data = [], $status = 200)
    {
        return response()->json([
            'data' => $data,
        ], $status);
    }

    public function failure(array $errors, $status = 400)
    {
        return response()->json([
            'errors' => $errors,
        ], $status);
    }
}
