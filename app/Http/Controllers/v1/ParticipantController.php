<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ParticipantCollection;
use App\Models\v1\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    public $relationships = [
        'action',
        'employee',
    ];

    public function __invoke(Request $request)
    {
        $included = $this->includeRelations($request);
        $filterParams = $this->getFilterParams($request);

        $participants = Participant::with($included ?: ['action']);

        if (!empty($filterParams)) {
            $ids = DB::table('action_employee')
                ->join('actions', 'action_id', '=', 'actions.id')
                ->join('employees', 'employee_id', '=', 'employees.id')
                ->join('formations', 'actions.formation_id', '=', 'formations.id')
                ->join('types', 'formations.type_id', '=', 'types.id')
                ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
                ->join('categories', 'formations.categorie_id', '=', 'categories.id')
                ->select('action_employee.id')
                ->where($filterParams)
                ->get()
                ->unique()
                ->pluck('id');

            $participants = $participants
                ->whereIn('id', $ids)
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        } else {
            $participants = $participants
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        }

        if (!$participants) {
            $this->failure([
                'message' => 'Aucun Formation correspondant n\'a été trouvé',
            ], 404);
        }

        return new ParticipantCollection($participants);
    }

    /**
     * Gets the filter parameters
     * @return array
     */
    private function getFilterParams(Request $request)
    {
        $result = [];
        $filterColumns = $request->query();
        $columns = [
            'direction',
            'csp',
            'sexe',
            'type',
            'domaine',
            'mode',
            'code_formation'
        ];
        foreach ($columns as $column) {
            $queryString = $filterColumns[Str::camel($column)] ?? null;

            if (isset($queryString)) {
                if ($column === 'domaine') {
                    $column = 'abbr';
                }
                $result[] = [$column, '=', $queryString];
            }
        }

        return $result;
    }
}
