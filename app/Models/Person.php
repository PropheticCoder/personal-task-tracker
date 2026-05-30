<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['name', 'role', 'email', 'phone', 'client_id'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(Dependency::class);
    }
}
