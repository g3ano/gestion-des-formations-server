<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ParticipantCollection;
use App\Models\v1\Participant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public $relationships = [
        'action',
        'employee',
    ];

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $included = $this->includeRelations($request);
        if (empty($included)) {
            $included = ['action'];
        }
        $participants = Participant::with($included ?? [])
            ->orderBy('updated_at', 'desc')
            ->paginate(25);

        if ($participants) {
            return new ParticipantCollection($participants);
        }

        throw new HttpResponseException(
            $this->failure([
                'message' => 'Aucun Formation correspondant n\'a été trouvé',
            ], 404)
        );
    }
}
