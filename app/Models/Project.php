<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name', 'workspace_id', 'client_id', 'status', 'description', 'color'];

    protected $attributes = [
        'status' => 'active',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(Dependency::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
