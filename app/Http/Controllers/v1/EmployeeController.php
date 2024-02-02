<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Employee\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\v1\Employee;
use App\Services\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use HttpResponseTrait;
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

        $id = DB::table('employees')->insertGetId([
            ...$data,
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
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
                if (isset($id)) {
                    $rows[] = DB::table('employees')->where('id', '=', $id)->delete();
                }
            }
        }

        if (count($rows) === count($ids)) {
            return $this->success([
                'message' => 'Employee(s) was deleted successfully',
                'effected rows' => count($rows),
            ]);
        }
        return $this->success([
            'message' => 'Some error has occurred',
        ]);
    }

    /**
     * Get the current timestamp
     * 
     * @return \Illuminate\Support\Carbon
     */
    private function timestamp()
    {
        return now();
    }
}
