<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'source', 'interest_area',
        'temperature', 'stage', 'next_contact', 'notes', 'converted_at',
    ];

    protected $casts = [
        'name'        => 'encrypted',
        'phone'       => 'encrypted',
        'next_contact' => 'date',
        'converted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($q) {
            if (auth()->check()) {
                $q->where('user_id', auth()->id());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function touchpoints()
    {
        return $this->morphMany(Touchpoint::class, 'touchable')->latest('contacted_at');
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }
}
