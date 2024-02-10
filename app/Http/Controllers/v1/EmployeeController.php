<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Employee\StoreEmployeeRequest;
use App\Http\Requests\v1\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = DB::table('employees')
            ->select()
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(
            EmployeeResource::collection($employees)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['date_naissance'] = new Carbon($data['date_naissance']);

        $id = DB::table('employees')->insertGetId([
            ...$data,
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        if ($id) {
            return $this->success([
                'message' => 'L\'Employee est crée',
                'effectedRowId' => $id,
            ]);
        }

        return $this->failure([
            'message' => 'L\'Employée n\'est pas crée',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = DB::table('employees')
            ->select()
            ->where('id', $id)
            ->first();

        if (!$employee) {
            return $this->failure([
                'message' => 'L\'Employée n\'est pas trouvé',
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
        $employee = DB::table('employees')
            ->select()
            ->where('id', $id)
            ->first();

        if (!$employee) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'L\'Employée n\'est pas trouvé',
                ], 404)
            );
        }

        $data = $request->validated();
        $data['updated_at'] = $this->timestamp();

        $row = DB::table('employees')
            ->select()
            ->where('id', $id)
            ->update($data);

        if ($row) {
            return $this->success([
                'message' => 'L\'Employée est modifiée',
                'effectedRow' => $row,
            ]);
        }

        return $this->failure([
            'message' => 'Error, L\'Employée n\'est pas modifiée',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $rows = [];
        $ids = $request->input('ids');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($id) {
                    $row = DB::table('employees')
                        ->where('id', $id)
                        ->delete();

                    if ($row) {
                        $rows[] = $row;
                    }
                }
            }
        }

        if (count($rows)) {
            return $this->success([
                'message' => count($rows) > 1
                    ? 'Employées ont été supprimés'
                    : 'Employé a été supprimé.',
                'effectedRows' => count($rows),
            ]);
        }

        $message = 'Provided ' .
            (count($ids) > 1 ? 'ids' : 'id') . ' doesn\'t match any record';

        return $this->failure([
            'message' => $message,
            'effectedRows' => count($rows),
        ]);
    }

    /**
     * Get the current timestamp
     *
     * @return \Illuminate\Support\Carbon
     */
    private function timestamp()
    {
        return Carbon::now();
    }
}
