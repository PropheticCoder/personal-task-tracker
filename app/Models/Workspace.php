<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    protected $fillable = ['name', 'color', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_user');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
