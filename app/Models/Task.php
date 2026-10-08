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
        'hot_lead'         => 'Hot Lead',
        'untouched_client' => 'Check In',
    ];

    protected $fillable = [
        'user_id', 'title', 'status', 'position', 'status_changed_at',
        'source_type', 'source_id', 'source_url',
        'due_date', 'is_priority', 'notes',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'due_date'          => 'date',
        'is_priority'       => 'boolean',
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

    /** Shape sent to the kanban-board Alpine module. */
    public function toBoard(): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'status'            => $this->status,
            'position'          => $this->position,
            'source_type'       => $this->source_type,
            'source_label'      => self::SOURCE_LABELS[$this->source_type] ?? null,
            'source_url'        => $this->source_url,
            'status_changed_at' => $this->status_changed_at?->toIso8601String(),
            'created_at'        => $this->created_at?->toIso8601String(),
            'due_date'          => $this->due_date?->toDateString(),
            'is_priority'       => (bool) $this->is_priority,
            'notes'             => $this->notes,
        ];
    }
}
