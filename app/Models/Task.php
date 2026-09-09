<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    public const STATUSES = ['backlog', 'today', 'doing', 'done'];

    // Auto-generated backlog sources — see TaskAutoBacklogService.
    public const SOURCE_LABELS = [
        'overdue_followup' => 'Follow-up',
        'renewal_due'      => 'Renewal',
        'hot_lead'         => 'Hot Lead',
    ];

    protected $fillable = ['user_id', 'title', 'status', 'position', 'status_changed_at', 'source_type', 'source_id'];

    protected $casts = [
        'status_changed_at' => 'datetime',
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
}
