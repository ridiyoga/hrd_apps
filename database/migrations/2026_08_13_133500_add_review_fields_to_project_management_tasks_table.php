<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_tasks', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('blocker_reason');
            }

            if (!Schema::hasColumn('project_management_tasks', 'reviewed_at')) {
                $table->timestampTz('reviewed_at')->nullable()->after('review_notes');
            }

            if (!Schema::hasColumn('project_management_tasks', 'reviewed_by')) {
                $table->string('reviewed_by')->nullable()->index()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            foreach (['reviewed_by', 'reviewed_at', 'review_notes'] as $column) {
                if (Schema::hasColumn('project_management_tasks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
