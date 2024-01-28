<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "categorie" => $this->categorie,
            "type" => $this->type,
            "intitule" => $this->intitule,
            "organisme" => $this->organisme,
            "code_domaine" => $this->code_domaine,
            "structure" => $this->structure,
            "code_formation" => $this->code_formation,
            "mode" => $this->mode,
            "lieu" => $this->lieu,
            "effectif" => $this->effectif,
            "durree" => $this->durree,
            "h_j" => $this->h_j,
            "observation" => $this->observation ? $this->observation : '',
            "categorie" => $this->categorie,
            "domaine" => $this->abbr,
            "pedagogiques" => $this->pedagogiques,
            "hebergement_restauration" => $this->hebergement_restauration,
            "transport" => $this->transport,
            "presalaire" => $this->presalaire,
            "autres_charges" => $this->autres_charges,
            "dont_devise" => $this->dont_devise,
            "created_at" => date('Y-m-d', strtotime($this->updated_at)),
        ];
    }
}
