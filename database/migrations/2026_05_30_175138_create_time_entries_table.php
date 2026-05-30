<?php

use Illuminate\Database\Migrations\Migration;

// Superseded — time is tracked via hours on activities, not a separate table.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
