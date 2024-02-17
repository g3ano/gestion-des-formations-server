<?php

namespace App\Http\Controllers;

use App\Services\HandleHttpResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HandleHttpResponse;

    protected $relationships;

    /**
     * Gets the relationships that should be included when retrieving data.
     * **PS:** Turn out that Laravel support nesting relationships with dot,
     * this only checks the parent relationship, 
     * and lets hope the nested relationship is valid
     * or a RelationNotFoundException is thrown by Laravel
     * @return array
     */
    protected function includeRelations(Request $request)
    {
        $isNested = false;
        $included = [];
        $query = $request->query('include');
        if (is_array($query) && !empty($query)) {
            $result = '';
            foreach ($query as $value) {
                $result = $value;
                if (str_contains($result, '.')) {
                    $expandedValue = explode('.', $result, 2);
                    $result = array_shift($expandedValue);
                    $isNested = true;
                }
                $result =  ctype_lower($result)
                    ? $result
                    : preg_replace_callback(
                        '/([A-Z])/',
                        function ($groups) {
                            return '_' . strtolower($groups[1]);
                        },
                        $result
                    );

                if (in_array($result, $this->relationships)) {
                    if ($isNested) {
                        $included[] = $result . '.' . $expandedValue[0];
                        $isNested = false;
                    } else {
                        $included[] = $result;
                    }
                }
            }
        }
        return $included;
    }
}
