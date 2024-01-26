<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domaine extends Model
{
    use HasFactory;

    public function formation(): HasMany
    {
        return $this->hasMany(Formation::class);
    }
}
