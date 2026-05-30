<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolRun extends Model
{
    protected $fillable = [
        'task_id', 'tool_key', 'parameters', 'input_path',
        'input_text', 'output_paths', 'status', 'error',
    ];

    protected $casts = [
        'parameters'   => 'array',
        'output_paths' => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
