<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('project_management_tasks', 'status_updated_at')) {
            return;
        }

        Schema::table('project_management_tasks', function (Blueprint $table) {
            $table->timestampTz('status_updated_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('project_management_tasks', 'status_updated_at')) {
            return;
        }

        Schema::table('project_management_tasks', function (Blueprint $table) {
            $table->dropColumn('status_updated_at');
        });
    }
};
