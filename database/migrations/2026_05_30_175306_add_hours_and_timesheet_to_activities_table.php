<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->decimal('hours', 5, 2)->nullable()->after('body');
            $table->foreignId('timesheet_id')->nullable()->after('hours')
                  ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['timesheet_id']);
            $table->dropColumn(['hours', 'timesheet_id']);
        });
    }
};
