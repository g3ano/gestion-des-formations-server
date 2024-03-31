<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Action extends Model
{
    use HasFactory;

    // TODO: use the fillable property instead
    protected $guarded = [];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)
            ->withTimestamps()
            ->withPivot('observation')
            ->using(Participant::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}
