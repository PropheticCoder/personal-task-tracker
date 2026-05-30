<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = ['project_id', 'title', 'held_at', 'notes'];

    protected $casts = [
        'held_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
