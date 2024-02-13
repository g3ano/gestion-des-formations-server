<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Formation\UpdateFormationRequest;
use App\Http\Requests\v1\Formation\StoreFormationRequest;
use App\Http\Resources\v1\FormationResource;
use App\Models\v1\Cout;
use App\Models\v1\Formation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FormationController extends Controller
{
    protected $relationships = [
        'cout',
        'domaine',
        'code_domaine',
        'type',
        'intitule',
        'organisme',
        'categorie',
        'actions',
    ];

    public function index(Request $request)
    {
        $includedRelations = $this->includeRelations($request);
        $formations = Formation::with($includedRelations)
            ->orderBy('updated_at', 'desc')
            ->get();

        if (!$formations) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'Pas des formations trouvées',
                ], 404)
            );
        }
        return $this->success(
            FormationResource::collection($formations)
        );
    }

    public function show(string $id, Request $request)
    {
        $includedRelations = $this->includeRelations($request);
        $formation = Formation::with($includedRelations)->where('id', $id)->first();

        if ($formation) {
            return $this->success(FormationResource::make($formation));
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun Formation correspondant n\'a été trouvé',
            ], 404)
        );
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
         *          //columns that will be inserted into cout table, then return the record id
         *          //these columns are calculated based on the common and/or direct columns,
         *          //e.g:
         *          //pédagogiques:
         *          // 1) h_j multiplied by a price specified by the organisme, lieu and mode
         *          // 2) effectif multiplied by a price specified by the organisme, lieu and
         *          //    mode
         *          //it's a total mess, basically all cout columns are like this
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

        $formationData['cout_id'] = Cout::create($data['cout'])->id;
        $formationData['h_j'] =
            (float) $data['direct']['effectif'] * (float) $data['direct']['durree'];

        $formation = Formation::create([
            ...$data['direct'],
            ...$formationData,
        ]);

        if ($formation) {
            return $this->success([
                'message' => 'La Formation a été ajoutée avec succès',
                'formationId' => $formation->id,
            ]);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer cette action',
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, string $id)
    {
        $formation = Formation::with(array_diff($this->relationships, ['actions']))->where('id', $id)->first();

        if (!$formation) {
            throw new HttpResponseException(
                $this->failure([
                    'message' => 'La Formation n\'est pas trouvée',
                ], 404)
            );
        }

        $data = $request->validated();

        $cout = [];
        foreach ($data['cout'] as $attr => $value) {
            $attrOldValue = $formation->cout->$attr;

            if ($attrOldValue && $attrOldValue !== $value) {
                $cout[$attr] = $value;
            }
        }

        if (count($cout)) {
            $cout = Cout::where('id', $formation->cout_id)
                ->update($cout);

            $formationData = [
                'cout_id' => $cout,
            ];
        }

        foreach ($data['common'] as $attr => $value) {
            $attrOldValue = $formation->$attr->$attr;
            $attrWithId = $attr . '_id';

            if ($attrOldValue !== $value) {
                $formationData[$attrWithId] = $this->getId($attr, $value);
            }
        }

        $formationData['h_j'] =
            (float) $data['direct']['effectif'] * (float) $data['direct']['durree'];

        $status = Formation::where('id', $formation->id)
            ->update([
                ...$data['direct'],
                ...$formationData,
            ]);

        if ($status) {
            return $this->success([
                'message' => 'La formation a été modifiée',
                'formationId' => $formation->id,
            ]);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Nous n\'avons pas pu effectuer l\'action.',
            ])
        );
    }

    /**
     * Removes the specified resource(s)
     * @return array
     */
    public function destroy(Request $request)
    {
        $rows = [];
        $ids = $request->input('ids');

        $rows = Formation::destroy($ids);

        if ($rows) {
            return $this->success([
                'message' => $rows > 1
                    ? 'Formations ont été supprimés'
                    : 'Formation a été supprimé.',
                'effectedRows' => $rows,
            ]);
        }

        throw new HttpResponseException($this->failure([
            'message' => 'La suppression a échoué, aucune correspondance n\'est trouvée',
        ]));
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
