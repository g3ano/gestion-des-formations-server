<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Employee\StoreEmployeeRequest;
use App\Http\Requests\v1\Employee\UpdateEmployeeRequest;
use App\Http\Resources\v1\EmployeeResource;
use App\Models\v1\Employee;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeController extends Controller
{
    protected $relationships = [
        'actions',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $includedRelations = $this->includeRelations($request);
        $employees = Employee::with($includedRelations)->get();

        if ($employees) {
            return $this->success(
                EmployeeResource::collection($employees),
            );
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun Employees correspondant n\'a été trouvé',
            ], 404)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['date_naissance'] = new Carbon($data['date_naissance']);

        $employee = Employee::create($data);

        if ($employee) {
            return $this->success([
                'message' => 'L\'Employee a été ajoutée avec succès',
                'employeeId' => $employee->id,
            ]);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $includedRelations = $this->includeRelations($request);
        $employee = Employee::with($includedRelations)
            ->where('id', $id)
            ->first();

        if (!$employee) {
            return $this->failure([
                'message' => 'L\'Employée n\'est pas trouvée',
            ], 404);
        }

        return $this->success(
            EmployeeResource::make($employee)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, string $id)
    {
        $employee = Employee::where('id', $id)
            ->first();

        if (!$employee) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'L\'Employée n\'est pas trouvée',
                ], 404)
            );
        }

        $data = $request->validated();

        $status = Employee::where('id', $id)
            ->update($data);

        if ($status) {
            return $this->success([
                'message' => 'L\'Employée est modifiée',
                'employeeId' => $employee->id,
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
        $rows = [];
        $ids = $request->input('ids');

        $rows = Employee::destroy($ids);

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
