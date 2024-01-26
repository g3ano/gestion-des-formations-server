<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intitule extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
    ];

    public function formation(): HasMany
    {
        return $this->hasMany(Formation::class);
    }
}
