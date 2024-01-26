<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodeDomaine extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_domaine',
    ];

    public function formation(): HasMany
    {
        return $this->hasMany(Formation::class);
    }
}
