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
        'pedagogiques' => 'decimal:2',
        'hebergement_restauration' => 'decimal:2',
        'transport' => 'decimal:2',
        'presalaire' => 'decimal:2',
        'autres_charges' => 'decimal:2',
        'dont_devise' => 'decimal:2',
    ];
}
