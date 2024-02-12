<?php

namespace App\Http\Controllers;

use App\Services\Traits\HandleHttpResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HandleHttpResponse;

    protected $relationships;

    /**
     * Gets the relationships that should be included in the response
     * @return array
     */
    protected function includeRelations(Request $request)
    {
        $included = [];
        $query = $request->query('include');
        if (is_array($query) && !empty($query)) {
            $relationship = '';
            foreach ($query as $value) {
                $relationship =  ctype_lower($value)
                    ? $value
                    : Str::snake($value);

                if (in_array($relationship, $this->relationships)) {
                    $included[] = $relationship;
                }
            }
        }
        return $included;
    }
}
