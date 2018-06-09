<?php

namespace Laravel\Flow;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
        'available_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    protected $dates = [
        'completed_at',
        'available_at',
        'started_at',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('available_at', '<=', Carbon::now());
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeNotStarted($query)
    {
        return $query->whereNull('started_at');
    }
}
