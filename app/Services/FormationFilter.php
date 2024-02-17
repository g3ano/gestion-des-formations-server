<?php

namespace App\Services;

use Illuminate\Http\Request;

class FormationFilter
{
    private static $columns = [
        "id" => [
            'eq',
            'gt',
            'lt',
            'gt',
            'lt',
        ],
        "categorie" => [
            'eq',
            'neq',
            'like',
        ],
        "type" => [
            'eq',
            'neq',
            'like',
        ],
        "intitule" => [
            'eq',
            'neq',
            'like',
        ],
        "organisme" => [
            'eq',
            'neq',
            'like',
        ],
        "code_domaine" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "structure" => [
            'eq',
            'neq',
            'like',
        ],
        "code_formation" => [
            'eq',
            'neq',
            'like',
        ],
        "mode" => [
            'eq',
            'neq',
            'like',
        ],
        "lieu" => [
            'eq',
            'neq',
            'like',
        ],
        "effectif" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "durree" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "observation" => [
            'eq',
            'neq',
            'like'
        ],
        "categorie" => [
            'eq',
            'neq',
            'like'
        ],
        "domaine" => [
            'eq',
            'neq',
            'like'
        ],
        "pedagogiques" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "hebergement_restauration" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "transport" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "presalaire" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "autres_charges" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "dont_devise" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
        "created_at" => [
            'eq',
            'neq',
            'gt',
            'lt',
            'gte',
            'lte',
        ],
    ];
    private static $operatorsTransformer = [
        'eq' => '=',
        'neq' => '!=',
        'gt' => '>',
        'lt' => '<',
        'gte' => '>=',
        'lte' => '<=',
        'like' => 'LIKE'
    ];

    public static $value = '';

    /**
     * Parses the given query string
     *
     * @param Request $request
     * @return array [[column, operator, value], ...]
     **/
    public static function parse(Request $request)
    {
        $result = [
            'query' => [],
            'boolean' => 'AND',
        ];

        foreach (static::$columns as $column => $operators) {
            $query = $request->query($column);

            if (!isset($query)) {
                continue;
            }

            if ($column === 'domaine') {
                $column = 'abbr';
            }

            foreach ($operators as $operator) {
                if (!isset($query[$operator])) {
                    continue;
                }

                if ($operator === 'like') {
                    $filtered = str_split(str_replace(' ', '', '%' . $query[$operator] . '%'));
                    static::$value = implode('%', $filtered);
                    $result['query'][] = [$column, static::$operatorsTransformer[$operator], static::$value];
                } else {
                    $result['query'][] = [$column, static::$operatorsTransformer[$operator], $query[$operator]];
                }
            }
        }

        return $result;
    }
}
