<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_management_projects') || Schema::hasColumn('project_management_projects', 'decisions_needed')) {
            return;
        }

        Schema::table('project_management_projects', function (Blueprint $table) {
            $table->text('decisions_needed')->nullable();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project_management_projects') || !Schema::hasColumn('project_management_projects', 'decisions_needed')) {
            return;
        }

        Schema::table('project_management_projects', function (Blueprint $table) {
            $table->dropColumn('decisions_needed');
        });
    }
};
