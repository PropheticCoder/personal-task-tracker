<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timesheet extends Model
{
    protected $fillable = [
        'workspace_id', 'task_id', 'type',
        'title', 'period_start', 'period_end',
        'client_id', 'notes', 'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function totalHours(): float
    {
        return (float) $this->activities->sum('hours');
    }

    public function totalHoursByProject(): \Illuminate\Support\Collection
    {
        return $this->activities
            ->whereNotNull('hours')
            ->groupBy('project_id')
            ->map(fn($entries) => [
                'project' => $entries->first()->project,
                'hours'   => $entries->sum('hours'),
                'entries' => $entries,
            ]);
    }
}
