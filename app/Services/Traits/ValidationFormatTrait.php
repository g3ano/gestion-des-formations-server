<?php

namespace App\Services\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

trait ValidationFormatTrait
{

    /**
     * Formats the coming request data
     * @return array
     */
    public function formatPreValidation(Request $request)
    {
        $formattedFields = [];
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $inner => $innerValue) {
                    $formattedFields[Str::snake($key)][Str::snake($inner)] = $innerValue;
                }
            } else {
                $formattedFields[Str::snake($key)] = $value;
            }
        }
        return $formattedFields;
    }

    /**
     * Align `MessageBag` assosiated with the current `Validator` instance,
     * to json response conventions
     * @return array
     */
    public function formatFailedValidation(MessageBag $_errors)
    {
        $result = [];
        $errors = $_errors->toArray();

        if (!$errors) return;

        foreach ($errors as $field => $errorMessages) {
            $formattedField = lcfirst(preg_replace(
                '/\s/',
                '',
                ucwords(preg_replace(
                    '/_/',
                    ' ',
                    $field
                ))
            ));

            $parsedKey = explode('.', $formattedField);
            if (count($parsedKey) !== 1) {
                $parent = array_shift($parsedKey);
                foreach ($parsedKey as $value) {
                    $result[$parent][$value] = $errorMessages;
                }
            } else {
                $result[$formattedField] = $errorMessages;
            }
        }
        return $result;
    }
}
