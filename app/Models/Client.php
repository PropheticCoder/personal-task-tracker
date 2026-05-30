<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'workspace_id'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
