<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Services\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormationFormController extends Controller
{
    use HttpResponseTrait;

    public function getIntitules(Request $request)
    {
        $intitule = strtolower($request->intitule);

        $intitules = DB::table('intitules')
            ->select(['id', 'intitule'])
            ->where('intitule', 'LIKE', $intitule . '%')
            ->get();

        return $this->success($intitules);
    }
}
