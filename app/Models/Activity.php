<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id', 'project_id', 'dependency_id', 'meeting_id',
        'type', 'body', 'hours', 'timesheet_id',
        'old_status', 'new_status', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'hours'      => 'float',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(Dependency::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }
}
