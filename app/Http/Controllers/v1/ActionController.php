<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Action\StoreActionRequest;
use App\Http\Requests\v1\Action\UpdateActionRequest;
use App\Http\Resources\v1\ActionCollection;
use App\Http\Resources\v1\ActionResource;
use App\Models\v1\Action;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    protected $relationships = [
        'formation',
        'employees',
    ];

    public function index(Request $request)
    {
        $included = $this->includeRelations($request);
        $filterParams = $this->getFilterParams($request);
        $actions = Action::with($included);

        if (!empty($filterParams)) {
            $ids = DB::table('action_employee')
                ->join('actions', 'action_id', '=', 'actions.id')
                ->join('employees', 'employee_id', '=', 'employees.id')
                ->join('formations', 'actions.formation_id', '=', 'formations.id')
                ->join('types', 'formations.type_id', '=', 'types.id')
                ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
                ->join('categories', 'formations.categorie_id', '=', 'categories.id')
                ->select('actions.id')
                ->where($filterParams)
                ->get()
                ->unique()
                ->pluck('id');

            $actions = $actions
                ->whereIn('id', $ids)
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
        } else {
            $actions = $actions
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
        }

        if (!$actions) {
            $this->failure([
                'message' => 'Aucun Formation correspondant n\'a été trouvé',
            ], 404);
        }

        return new ActionCollection($actions);
    }

    public function store(StoreActionRequest $request)
    {
        $data = $request->validated();

        $startDate =
            Carbon::createFromTimestamp($data['action']['date_debut']);
        $dueDate =
            Carbon::createFromTimestamp($data['action']['date_fin']);

        if ($dueDate->lessThanOrEqualTo($startDate)) {
            $this->failure([
                'errors' => [
                    'date fin' =>
                    "La date d'échéance ne peut être inférieure ou égale à la date de début",
                ]
            ], 422);
        }

        $data['action']['date_debut'] = $startDate->toDateString();
        $data['action']['date_fin'] = $dueDate->toDateString();

        /**
         * @var Action $action
         */
        $action = Action::create($data['action']);

        if (!$action) {
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ], 500);
        }


        foreach ($data['participants'] as $participant) {
            $action->employees()->attach(
                $participant['employee_id'],
                ['observation' => $participant['observation']]
            );
        }

        return $this->success([
            'message' => 'L\'Action a été ajoutée avec succès',
            'actionId' => $action->id,
        ], 201);
    }

    public function show(string $id, Request $request)
    {
        $included = $this->includeRelations($request);

        /** 
         * @var Action $action
         */
        $action = Action::with($included)
            ->where('id', $id)
            ->first();

        if (!$action) {
            $this->failure([
                'message' => 'Aucun Action correspondant n\'a été trouvé',
            ], 404);
        }

        $activeEmployees = [];

        foreach ($action->employees as $employee) {
            if (
                $employee->pivot->created_at->greaterThanOrEqualTo($action->date_debut) && $employee->pivot->created_at->lessThanOrEqualTo($action->date_fin)
            ) {
                $employee->isActive = true;

                //For backward compatibility only
                $activeEmployees[] = [
                    'id' => $employee->id,
                    'startedAt' => strtotime($employee->pivot->created_at),
                ];
            } else {
                $employee->isActive = false;
            }
        }

        //For backward compatibility only
        $action->activeEmployees = $activeEmployees;

        return $this->success(
            new ActionResource($action)
        );
    }

    public function update(UpdateActionRequest $request, string $id)
    {
        $action = Action::with($this->relationships)
            ->where('id', $id)
            ->first();

        if (!$action) {
            $this->failure([
                'message' => 'L\'Action n\est pas trouvé',
            ], 404);
        }

        $data = $request->validated();

        $startDate =
            Carbon::createFromTimestamp($data['action']['date_debut']);
        $dueDate =
            Carbon::createFromTimestamp($data['action']['date_fin']);

        if ($dueDate->lessThanOrEqualTo($startDate)) {
            $this->failure([
                'errors' => [
                    'date fin' =>
                    "La date d'échéance ne peut être inférieure ou égale à la date de début",
                ]
            ], 422);
        }

        $data['action']['date_debut'] = $startDate->toDateString();
        $data['action']['date_fin'] = $dueDate->toDateString();

        /**
         * @var BelongsToMany $employees
         */
        $employees = $action->employees();
        $employees->sync($data['participants']);

        $status = $action->update($data['action']);

        if ($status) {
            return $this->success([
                'message' => 'L\'Action est modifiée',
                'actionId' => $action->id,
            ]);
        }

        $this->failure([
            'message' => 'Nous n\'avons pas pu effectuer cette action',
        ]);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
        ]);

        $rows = Action::destroy($validated['ids']);

        if ($rows) {
            return $this->success([
                'message' => $rows > 1
                    ? 'Employées ont été supprimés'
                    : 'Employé a été supprimé.',
                'effectedRows' => $rows,
            ]);
        }

        $this->failure([
            'message' => 'Aucun résultat correspondant n\'a été trouvé',
        ], 404);
    }

    /**
     * Gets the filter parameters
     * 
     * @return array
     */
    private function getFilterParams(Request $request)
    {
        $result = [];
        $filterColumns = $request->query();
        $columns = [
            'direction',
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
