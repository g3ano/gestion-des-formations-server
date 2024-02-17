<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Participant extends Pivot
{
    protected $table = 'action_employee';
    public $incrementing = true;

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
