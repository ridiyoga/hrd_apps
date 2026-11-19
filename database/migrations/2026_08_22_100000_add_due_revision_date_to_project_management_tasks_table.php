<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_tasks', 'due_revision_date')) {
                $table->date('due_revision_date')->nullable()->after('due_date')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('project_management_tasks', 'due_revision_date')) {
                $table->dropColumn('due_revision_date');
            }
        });
    }
};
