<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Formation\UpdateFormationRequest;
use App\Http\Requests\v1\Formation\StoreFormationRequest;
use App\Http\Resources\FormationResource;
use App\Services\Filters\FormationFilter;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormationController extends Controller
{
    private $sql = [
        'formations.*',
        'categories.categorie',
        'domaines.abbr as domaine',
        'types.type',
        'intitules.intitule',
        'organismes.organisme',
        'code_domaines.code_domaine',
        'couts.pedagogiques',
        'couts.hebergement_restauration',
        'couts.transport',
        'couts.presalaire',
        'couts.autres_charges',
        'couts.dont_devise'
    ];


    public function index(Request $request)
    {
        $filters = FormationFilter::parse($request);
        $formations = DB::table('formations')
            ->join('categories', 'formations.categorie_id', '=', 'categories.id')
            ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
            ->join('types', 'formations.type_id', '=', 'types.id')
            ->join('intitules', 'formations.intitule_id', '=', 'intitules.id')
            ->join('organismes', 'formations.organisme_id', '=', 'organismes.id')
            ->join('code_domaines', 'formations.code_domaine_id', '=', 'code_domaines.id')
            ->join('couts', 'formations.cout_id', '=', 'couts.id')
            ->select($this->sql)
            ->where($filters['query'], null, null, $filters['boolean'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->success(
            FormationResource::collection($formations)
        );
    }

    public function show(string $id)
    {
        $formation = DB::table('formations')
            ->join('categories', 'formations.categorie_id', '=', 'categories.id')
            ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
            ->join('types', 'formations.type_id', '=', 'types.id')
            ->join('intitules', 'formations.intitule_id', '=', 'intitules.id')
            ->join('organismes', 'formations.organisme_id', '=', 'organismes.id')
            ->join('code_domaines', 'formations.code_domaine_id', '=', 'code_domaines.id')
            ->join('couts', 'formations.cout_id', '=', 'couts.id')
            ->select($this->sql)
            ->where('formations.id', $id)
            ->first();

        if (!$formation) {
            return $this->failure([
                'message' => 'La Formation n\'est pas trouvé',
            ]);
        }

        return $this->success(FormationResource::make($formation));
    }

    public function store(StoreFormationRequest $request)
    {
        /**
         * coming data should be formatted like this
         * [
         *      'direct' => [
         *          //columns that will inserted directly to db
         *      ],
         *      'common' => [
         *          //columns that could be common with many formations or could be unique
         *          //relationship columns, they gonna be replaced by matching ID from db,
         *          //or new record will be created, if not found
         *      ],
         *      'cout' => [
         *          //columns that will be inserted into couts table, then return the record id
         *          //these columns are calculated based on the common and/or direct columns,
         *          //e.g:
         *          //pédagogiques:
         *          // 1) h_j multiplied by a price specified by the organisme, lieu and mode
         *          // 2) effectif multiplied by a price specified by the organisme, lieu and
         *          //    mode
         *          //it's a total mess, basically all couts columns are like this
         *          //more investigation on this later
         *          //the goal is to find a clear pattern or formula
         *          //for now i'll do it manually
         *          //same thing applies for dont_device
         *      ],
         * ]
         */
        $data = $request->validated();

        $formationData = [];

        if (isset($data['common'])) {
            foreach ($data['common'] as $attr => $value) {
                $formationData[$attr . '_id'] = $this->getId($attr, $value);
            }
        }

        $formationData['cout_id'] = DB::table('couts')->insertGetId([
            ...$data['cout'],
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        $formationData['h_j'] = (float) $data['direct']['effectif'] * (float) $data['direct']['durree'];

        $id = DB::table('formations')->insertGetId([
            ...$data['direct'],
            ...$formationData,
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
        ]);

        if ($id) {
            return $this->success([
                'message' => 'La Formation est crée',
                'effectedRowId' => $id,
            ]);
        }

        return $this->failure([
            'message' => 'Error, La formation n\'est pas crée',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, string $id)
    {
        $formation = DB::table('formations')
            ->join('categories', 'formations.categorie_id', '=', 'categories.id')
            ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
            ->join('types', 'formations.type_id', '=', 'types.id')
            ->join('intitules', 'formations.intitule_id', '=', 'intitules.id')
            ->join('organismes', 'formations.organisme_id', '=', 'organismes.id')
            ->join('code_domaines', 'formations.code_domaine_id', '=', 'code_domaines.id')
            ->join('couts', 'formations.cout_id', '=', 'couts.id')
            ->select($this->sql)
            ->where('formations.id', $id)
            ->first();

        if (!$formation) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'La Formation n\'est pas trouvé',
                ], 404)
            );
        }

        $data = $request->validated();

        $couts = [];
        foreach ($data['cout'] as $attr => $value) {
            $attrOldValue = $formation->$attr;

            if ($attrOldValue !== $value) {
                $couts[$attr] = $value;
            }
        }

        if (count($couts)) {
            $couts['updated_at'] = $this->timestamp();
            DB::table('couts')
                ->select()
                ->where('id', $formation->cout_id)
                ->update($couts);
        }

        $formationData = [
            'cout_id' => $formation->cout_id,
        ];

        foreach ($data['common'] as $attr => $value) {
            $attrOldValue = $formation->$attr;
            $attrWithId = $attr . '_id';

            $formationData[$attrWithId] = $value !== $attrOldValue
                ? $this->getId($attr, $value)
                : $formation->$attrWithId;
        }

        $formationData['h_j'] = $data['direct']['effectif'] * $data['direct']['durree'];
        $formationData['h_j'] = $data['direct']['effectif'] * $data['direct']['durree'];
        $formationData['updated_at'] = $this->timestamp();


        $rows = DB::table('formations')
            ->where('id', $formation->id)
            ->update([
                ...$data['direct'],
                ...$formationData,
            ]);

        if ($rows) {
            return $this->success([
                'message' => 'La formation a été modifiée',
                'effectedRows' => $rows,
            ]);
        }

        return $this->failure([
            'message' => 'Error, La formation n\'est pas modifiée',
        ]);
    }

    /**
     * Removes the specified resource(s)
     * @return array
     */
    public function destroy(Request $request)
    {
        $rows = [];
        $ids = $request->input('ids');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                if ($id) {
                    $row = DB::table('formations')
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
                    ? 'Formations ont été supprimés'
                    : 'Formation a été supprimé.',
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
     * Get the current Timestamp
     *
     * @return \Illuminate\Support\Carbon
     */
    private function timestamp()
    {
        return Carbon::now();
    }

    /**
     * Get the row id if search value is found,
     * if the value doesn't exist create a new record from the value then get id,
     * @param string $attr column to search for in the table
     * @param string $value column value
     * @return int|null
     **/
    private function getId(string $attr, string $value)
    {
        $tables = ['intitules', 'code_domaines', 'organismes'];
        $tableName = strtolower(trim($attr)) . 's';
        $isAvailable = in_array($tableName, $tables, true);



        if ($isAvailable && Schema::hasTable($tableName)) {
            $record = DB::table($tableName)
                ->select('id')
                ->where($attr, '=', $value)
                ->first();

            if ($record) {
                return $record->id;
            }

            return DB::table($tableName)->insertGetId([
                $attr => $value,
                'updated_at' => $this->timestamp(),
                'created_at' => $this->timestamp(),
            ]);
        }

        return null;
    }

    /**
     * Sort a column
     *
     * @param Request $request the request send from the client
     * @return FormationResource
     **/
    public function sortColumn(Request $request)
    {
        //http://.../sort[direction]=column
        //This needs some filtering and to sanitize the coming data

        $queryString = $request->query();
        $column = array_key_first($queryString);
        $direction = $queryString[$column];

        $formations = DB::table('formations')
            ->join('categories', 'formations.categorie_id', '=', 'categories.id')
            ->join('domaines', 'formations.domaine_id', '=', 'domaines.id')
            ->join('types', 'formations.type_id', '=', 'types.id')
            ->join('intitules', 'formations.intitule_id', '=', 'intitules.id')
            ->join('organismes', 'formations.organisme_id', '=', 'organismes.id')
            ->join('code_domaines', 'formations.code_domaine_id', '=', 'code_domaines.id')
            ->join('couts', 'formations.cout_id', '=', 'couts.id')
            ->select($this->sql)
            ->orderBy($column, $direction)
            ->limit(20)
            ->get();

        return $this->success(
            FormationResource::collection($formations)
        );
    }

    /**
     * Get common values for the formation.
     * **PS:** common values are values that can be found in multiple formations,
     *  they also could be unique to only one formation
     * @return array
     **/
    public function getCommonValues()
    {

        $intitules = DB::table('intitules')
            ->select(['intitule'])
            ->get()
            ->pluck('intitule');
        $organismes = DB::table('organismes')
            ->select(['organisme'])
            ->get()
            ->pluck('organisme');
        $code_domaines = DB::table('code_domaines')
            ->select(['code_domaine'])
            ->get()
            ->pluck('code_domaine');

        return $this->success([
            'intitules' => $intitules,
            'organismes' => $organismes,
            'codeDomaines' => $code_domaines,
        ]);
    }
}
