<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependency extends Model
{
    protected $fillable = [
        'direction', 'person_id', 'task_id', 'project_id',
        'description', 'status', 'needed_by', 'resolved_at',
    ];

    protected $casts = [
        'needed_by'   => 'date',
        'resolved_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'open',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isOverdue(): bool
    {
        return $this->needed_by && $this->needed_by->isPast() && $this->status === 'open';
    }
}
