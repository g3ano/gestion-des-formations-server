<?php

namespace App\Http\Requests\v1;

use App\Services\FormatRequest;
use App\Services\HandleHttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    use FormatRequest, HandleHttpResponse;

    protected $stopOnFirstFailure = true;

    protected function prepareForValidation()
    {
        $this->merge(
            $this->formatPreValidation($this->all()),
        );
    }

    protected function failedValidation(Validator $validator)
    {
        $formattedErrors = $this->formatFailedValidation($validator->errors()->toArray());

        $this->failure(
            $formattedErrors,
            422
        );
    }
}
