<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id', 'meeting_id', 'title', 'description',
        'status', 'priority', 'needed_by', 'source',
    ];

    protected $casts = [
        'needed_by' => 'date',
    ];

    protected $attributes = [
        'status' => 'todo',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(Dependency::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function toolRuns(): HasMany
    {
        return $this->hasMany(ToolRun::class);
    }

    public function timesheet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Timesheet::class);
    }

    public function totalHours(): float
    {
        return (float) $this->activities()->whereNotNull('hours')->sum('hours');
    }

    /** Get or create the auto-timesheet for this task. */
    public function getOrCreateTimesheet(): Timesheet
    {
        return $this->timesheet ?? Timesheet::create([
            'task_id'      => $this->id,
            'type'         => 'task',
            'title'        => $this->title,
            'period_start' => $this->created_at->toDateString(),
            'period_end'   => now()->toDateString(),
            'project_id'   => null,
        ]);
    }

    /** The single open waiting_on dependency (if any). */
    public function blocker()
    {
        return $this->hasOne(Dependency::class)
            ->where('direction', 'waiting_on')
            ->where('status', 'open');
    }

    /** Open i_owe dependencies — who is depending on my output. */
    public function commitments(): HasMany
    {
        return $this->hasMany(Dependency::class)
            ->where('direction', 'i_owe')
            ->where('status', 'open');
    }
}
