<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    // TODO: use the fillable property instead
    protected $guarded = [];

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class)
            ->withTimestamps()
            ->withPivot('observation')
            ->using(Participant::class);;
    }
}
