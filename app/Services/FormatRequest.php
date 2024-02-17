<?php

namespace App\Services;

use Illuminate\Support\Str;

trait FormatRequest
{
    /**
     * Formats the coming request data
     * @return array
     */
    public function formatPreValidation(array $arr)
    {
        $formattedFields = [];
        foreach ($arr as $key => $value) {
            if (!empty($value) && is_array($value)) {
                $formattedFields[Str::snake($key)] = $this->formatPreValidation($value);
            } else {
                $formattedFields[Str::snake($key)] = $value;
            }
        }
        return $formattedFields;
    }

    /**
     * Expands the flat array given to us by Laravel
     * @return array
     */
    public function formatFailedValidation(array $errorsArr)
    {
        $result = [];
        foreach ($errorsArr as $field => $errorMessages) {
            $formattedField = preg_replace_callback('/(_)(.)/', function ($groups) {
                return strtoupper($groups[2]);
            }, strtolower($field));

            if (!str_contains($formattedField, '.')) {
                $result[$formattedField] = $errorMessages[0];
            } else {
                $segments = explode('.', $formattedField);
                $key = array_shift($segments);

                if (isset($result[$key])) {
                    $result[$key] += $this->nest($segments, $errorMessages[0]);
                } else {
                    $result[$key] = $this->nest($segments, $errorMessages[0]);
                }
            }
        }
        return $result;
    }

    private function nest(array $arr, string $message)
    {
        if (!$arr) {
            return;
        }

        $key = array_shift($arr);

        if (!$arr) {
            return [$key => $message];
        }

        return [$key => $this->nest($arr, $message)];
    }
}
