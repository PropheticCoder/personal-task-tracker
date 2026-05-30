<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_person', function (Blueprint $table) {
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->primary(['meeting_id', 'person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_person');
    }
};
