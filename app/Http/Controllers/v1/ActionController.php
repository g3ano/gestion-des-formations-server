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
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    protected $relationships = [
        'formation',
        'employees',
    ];

    /**
     * Display a listing of the resource.
     */
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
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'Aucun Formation correspondant n\'a été trouvé',
                ], 404)
            );
        }

        return new ActionCollection($actions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActionRequest $request)
    {
        $data = $request->validated();

        $data['action']['date_debut'] = date('Y-m-d', $data['action']['date_debut']);
        $data['action']['date_fin'] = date('Y-m-d', $data['action']['date_fin']);

        /**
         * @var Action $action
         */
        $action = Action::create($data['action']);

        foreach ($data['participants'] as $participant) {
            $action->employees()->attach(
                $participant['employee_id'],
                ['observation' => $participant['observation']]
            );
        }

        if ($action) {
            return $this->success([
                'message' => 'L\'Action a été ajoutée avec succès',
                'actionId' => $action->id,
            ], 201);
        }

        throw new HttpResponseException($this->failure([
            'message' => 'Nous n\'avons pas pu effectuer cette action',
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $included = $this->includeRelations($request);
        /** @var Action $action */
        $action = Action::with($included)->where('id', $id)->first();

        if (!$action) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'Aucun Action correspondant n\'a été trouvé',
                ], 404)
            );
        }

        if (Carbon::make($action->date_fin)->greaterThanOrEqualTo(Carbon::now())) {
            $activeEmployees = [];

            foreach ($action->employees as $employee) {
                if (
                    $employee->pivot->created_at->greaterThanOrEqualTo($action->date_debut) && $employee->pivot->created_at->lessThanOrEqualTo($action->date_fin)
                ) {
                    $activeEmployees[] = $employee->id;
                }
            }

            $action->activeEmployees = $activeEmployees;
        }

        return $this->success(
            ActionResource::make($action)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActionRequest $request, string $id)
    {
        $action = Action::with($this->relationships)
            ->where('id', $id)
            ->first();

        if (!$action) {
            throw new HttpResponseException($this->failure([
                'message' => 'L\'Action n\est pas trouvé',
            ], 404));
        }

        $data = $request->all();

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

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
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

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun résultat correspondant n\'a été trouvé',
            ], 404),
        );
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
