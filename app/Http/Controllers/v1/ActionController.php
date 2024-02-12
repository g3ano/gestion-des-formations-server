<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Action\StoreActionRequest;
use App\Http\Requests\v1\Action\UpdateActionRequest;
use App\Http\Resources\v1\ActionResource;
use App\Models\v1\Action;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public $relationships = [
        'formation',
        'employees'
    ];

    private function includeRelations(Request $request)
    {
        $included = [];
        $query = $request->query('include');
        if (is_array($query) && !empty($query)) {
            foreach ($this->relationships as $relationship) {
                if (in_array($relationship, $query)) {
                    $included[] = $relationship;
                }
            }
        }
        return $included;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $included = $this->includeRelations($request);
        $actions = Action::with($included)
            ->orderBy('updated_at', 'desc')
            ->paginate(2);

        if ($actions) {
            return $this->success([
                'actions' => ActionResource::collection($actions),
            ]);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun Formation correspondant n\'a été trouvé',
            ], 404)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActionRequest $request)
    {
        $data = $request->validated();

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
        $action = Action::with($included)->where('id', $id)->first();

        if (!$action) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'Aucun Action correspondant n\'a été trouvé',
                ], 404)
            );
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
                'status' => 'successful',
                'action' => ActionResource::make($action),
            ]);
        }
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
                'message' => 'L\'action a été supprimée avec succès.',
                'effectedRows' => $rows,
            ]);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun résultat correspondant n\'a été trouvé',
            ], 404),
        );
    }
}
