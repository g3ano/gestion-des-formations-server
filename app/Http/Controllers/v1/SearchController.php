<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ActionCollection;
use App\Http\Resources\v1\EmployeeCollection;
use App\Http\Resources\v1\FormationCollection;
use App\Models\v1\Action;
use App\Models\v1\Employee;
use App\Models\v1\Formation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $page = $request->query('page') ?? 1;
        $limit = $request->query('limit') ?? 25;
        $isShuffled = $request->query('isShuffled') ?? false;
        $includes = $this->includeRelations($request);

        if (!$includes) {
            $includes = ['actions'];
        }

        $query  = (string) $request->query('query') ?? '';

        if (!$query) {
            return;
        }

        $result = collect();

        if (in_array('actions', $includes) || in_array('formations', $includes)) {
            $formations = Formation::with(['intitule'])
                ->whereHas('intitule', function (Builder $builder) use ($query) {
                    $builder->where('intitule', 'LIKE', '%' . $query . '%');
                })
                ->paginate($limit, ['*'], 'page', $page);


            if (in_array('formations', $includes)) {
                $collection = new FormationCollection($formations);
                $result['formations'] = $collection->collection;
            }

            if (in_array('actions', $includes)) {
                $formationsId = collect($formations->items())
                    ->pluck('id')
                    ->flatten(1);

                /**
                 * @var LengthAwarePaginator $actions
                 */
                $actions = Action::with(['formation.intitule'])
                    ->whereIn('formation_id', $formationsId)
                    ->paginate($limit, ['*'], 'page', $page);

                if ($actions) {
                    $collection = new ActionCollection($actions);
                    $result['actions'] = $collection->collection;
                }
            }
        }

        if (in_array('employees', $includes)) {
            /**
             * @var LengthAwarePaginator $employees
             */
            $employees = Employee::where(function (Builder $builder) use ($query) {
                $builder->where('nom', 'LIKE',  '%' . $query . '%')
                    ->orWhere('prenom', 'LIKE', '%' . $query . '%');
            })
                ->paginate($limit, ['*'], 'page', $page);

            if ($employees) {
                $collection = new EmployeeCollection($employees);
                $result['employees'] = $collection->collection;
            }
        }

        $pages = ${$includes[0]}->lastPage();
        foreach ($includes as $value) {
            $current = ${$value}->lastPage();
            if ($pages < $current) {
                $pages = $current;
            }
        }

        $data = $isShuffled
            ? $result->flatten(1)->shuffle()->all()
            : $result;

        $pagination = [
            'page' => ${$includes[0]}->currentPage(),
            'pages' => $pages,
        ];

        //TODO: optimize the pagination limit for the employees
        //don't fetch new employees when the limit is hit
        return $this->success([
            'data' => $data,
            'pagination' => $pagination,
        ], 200, true);
    }
}
