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
}
