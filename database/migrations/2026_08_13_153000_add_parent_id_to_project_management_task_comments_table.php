<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_management_task_comments')) {
            return;
        }

        Schema::table('project_management_task_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_task_comments', 'parent_id')) {
                $table->uuid('parent_id')->nullable()->after('task_id')->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('project_management_task_comments')) {
            return;
        }

        Schema::table('project_management_task_comments', function (Blueprint $table) {
            if (Schema::hasColumn('project_management_task_comments', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });
    }
};
