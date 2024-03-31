<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ActionCollection;
use App\Http\Resources\v1\EmployeeResource;
use App\Http\Resources\v1\FormationResource;
use App\Models\v1\Action;
use App\Models\v1\Employee;
use App\Models\v1\Formation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $relationships = [
        'actions',
        'formations',
        'employees',
    ];

    public function __invoke(Request $request)
    {
        $includes = $this->includeRelations($request);

        if (!$includes) {
            $includes = ['actions'];
        }

        $query  = (string) $request->query('query') ?? '';

        if (!$query) {
            return;
        }

        $result = [];

        if (in_array('actions', $includes) || in_array('formations', $includes)) {
            $formations = Formation::with(['intitule'])
                ->whereHas('intitule', function (Builder $builder) use ($query) {
                    $builder->where('intitule', 'LIKE', '%' . $query . '%');
                })
                ->limit(100)
                ->get();

            if (in_array('formations', $includes)) {
                $result['formations'] = FormationResource::collection($formations);
            }

            if (in_array('actions', $includes)) {
                $actions = Action::with(['formation.intitule'])
                    ->whereIn('formation_id', $formations->pluck('id')->flatten(1))
                    ->limit(100)
                    ->get();

                if ($actions) {
                    $result['actions'] = new ActionCollection($actions);
                }
            }
        }

        if (in_array('employees', $includes)) {
            $employees = Employee::where(function (Builder $builder) use ($query) {
                $builder->where('nom', 'LIKE',  '%' . $query . '%')
                    ->orWhere('prenom', 'LIKE', '%' . $query . '%');
            })
                ->limit(100)
                ->get();

            $result['employees'] = EmployeeResource::collection($employees);
        }

        return $this->success($result);
    }
}
