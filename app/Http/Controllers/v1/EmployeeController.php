<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Employee\StoreEmployeeRequest;
use App\Http\Requests\v1\Employee\UpdateEmployeeRequest;
use App\Http\Resources\v1\EmployeeResource;
use App\Models\v1\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $relationships = [
        'actions',
    ];

    public function index(Request $request)
    {
        $includedRelations = $this->includeRelations($request);

        $employees = Employee::with($includedRelations)
            ->orderBy('updated_at', 'desc')
            ->get();

        if (!$employees) {
            $this->failure([
                'message' => 'Aucun Employees correspondant n\'a été trouvé',
            ], 404);
        }

        return $this->success(
            EmployeeResource::collection($employees),
        );
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['date_naissance'] = date('Y-m-d', $data['date_naissance']);

        $employee = Employee::create($data);

        if ($employee) {
            return $this->success([
                'message' => 'L\'Employee a été ajoutée avec succès',
                'employeeId' => $employee->id,
            ]);
        }

        $this->failure([
            'message' => 'Nous n\'avons pas pu effectuer cette action',
        ]);
    }

    public function show(string $id, Request $request)
    {
        $includedRelations = $this->includeRelations($request);
        $employee = Employee::with($includedRelations)
            ->where('id', $id)
            ->first();

        if (!$employee) {
            $this->failure([
                'message' => 'L\'Employée n\'est pas trouvée',
            ], 404);
        }

        return $this->success(
            EmployeeResource::make($employee)
        );
    }

    public function update(UpdateEmployeeRequest $request, string $id)
    {
        $employee = Employee::where('id', $id)
            ->first();

        if (!$employee) {
            $this->failure([
                'message' => 'L\'Employée n\'est pas trouvée',
            ], 404);
        }

        $data = $request->validated();
        $data['date_naissance'] = date('Y-m-d', $data['date_naissance']);

        $status = Employee::where('id', $id)
            ->update($data);

        if (!$status) {
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ]);
        }

        return $this->success([
            'message' => 'L\'Employée est modifiée',
            'employeeId' => $employee->id,
        ]);
    }

    public function destroy(Request $request)
    {
        $rows = [];
        $ids = $request->input('ids');

        $rows = Employee::destroy($ids);

        if (!$rows) {
            $this->failure([
                'message' => 'Aucun résultat correspondant n\'a été trouvé',
            ], 404);
        }

        return $this->success([
            'message' => $rows > 1
                ? 'Employées ont été supprimés'
                : 'Employé a été supprimé.',
            'effectedRows' => $rows,
        ]);
    }
}
