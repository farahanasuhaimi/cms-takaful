<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['email', 'token', 'invited_by', 'used_at', 'expires_at'];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }
}
