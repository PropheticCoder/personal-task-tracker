<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('dependency_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['note', 'status_change', 'dependency_opened', 'dependency_resolved', 'task_added', 'meeting_held']);
            $table->text('body')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
