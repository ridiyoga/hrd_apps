<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_tasks', 'developer_update_notes')) {
                $table->text('developer_update_notes')->nullable()->after('acceptance_criteria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('project_management_tasks', 'developer_update_notes')) {
                $table->dropColumn('developer_update_notes');
            }
        });
    }
};
