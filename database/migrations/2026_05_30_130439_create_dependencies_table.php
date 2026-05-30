<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencies', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['waiting_on', 'i_owe']);
            $table->foreignId('person_id')->constrained()->restrictOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->date('needed_by')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependencies');
    }
};
