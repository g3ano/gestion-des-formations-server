<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cout extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedagogiques',
        'hebergement_restauration',
        'transport',
        'presalaire',
        'autres_charges',
        'dont_devise',
    ];

    public function formation(): HasMany
    {
        return $this->hasMany(Formation::class);
    }

    protected $casts = [
        'pedagogiques' => 'float',
        'hebergement_restauration' => 'float',
        'transport' => 'float',
        'presalaire' => 'float',
        'autres_charges' => 'float',
        'dont_devise' => 'float',
    ];
}
