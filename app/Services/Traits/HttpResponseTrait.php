<?php

namespace App\Services\Traits;

trait HttpResponseTrait
{
    public function success($data = [], $status = 200)
    {
        return response()->json($data, $status);
    }

    public function failure($errors, $status = 400)
    {
        return response()->json([
            'error' => $errors,
        ], $status);
    }
}
