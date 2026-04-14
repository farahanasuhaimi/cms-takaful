<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'source', 'interest_area',
        'temperature', 'stage', 'next_contact', 'notes', 'converted_at',
    ];

    protected $casts = [
        'next_contact' => 'date',
        'converted_at' => 'datetime',
    ];

    public function touchpoints()
    {
        return $this->morphMany(Touchpoint::class, 'touchable')->latest('contacted_at');
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }
}
