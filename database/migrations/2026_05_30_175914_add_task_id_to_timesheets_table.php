<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            // nullable + unique: each task has at most one auto-timesheet
            $table->foreignId('task_id')->nullable()->unique()
                  ->after('id')->constrained()->cascadeOnDelete();
            // type distinguishes auto-created (task) from manually assembled (combined)
            $table->enum('type', ['task', 'combined'])->default('task')->after('task_id');
        });
    }

    public function down(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropColumn(['task_id', 'type']);
        });
    }
};
