<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')
                  ->constrained()->nullOnDelete();
        });

        Schema::table('timesheets', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')
                  ->constrained()->nullOnDelete();
        });

        // Assign all existing records to the first user's default workspace
        $defaultWorkspaceId = DB::table('users')
            ->whereNotNull('default_workspace_id')
            ->value('default_workspace_id');

        if ($defaultWorkspaceId) {
            DB::table('clients')->whereNull('workspace_id')
                ->update(['workspace_id' => $defaultWorkspaceId]);

            DB::table('timesheets')->whereNull('workspace_id')
                ->update(['workspace_id' => $defaultWorkspaceId]);

            // Also fix projects that were created before workspace scoping
            DB::table('projects')->whereNull('workspace_id')
                ->update(['workspace_id' => $defaultWorkspaceId]);
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropColumn('workspace_id');
        });

        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropForeign(['workspace_id']);
            $table->dropColumn('workspace_id');
        });
    }
};
