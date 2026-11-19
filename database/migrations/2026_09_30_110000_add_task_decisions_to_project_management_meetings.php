<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_meeting_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_meeting_tasks', 'decision')) {
                $table->string('decision', 30)->default('accepted')->index();
            }
            if (!Schema::hasColumn('project_management_meeting_tasks', 'return_expectation')) {
                $table->text('return_expectation')->nullable();
            }
            if (!Schema::hasColumn('project_management_meeting_tasks', 'revision_due_date')) {
                $table->date('revision_due_date')->nullable();
            }
            if (!Schema::hasColumn('project_management_meeting_tasks', 'decided_by')) {
                $table->string('decided_by')->nullable();
            }
            if (!Schema::hasColumn('project_management_meeting_tasks', 'decided_at')) {
                $table->timestampTz('decided_at')->nullable();
            }
        });

        Schema::table('project_management_meeting_action_items', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_meeting_action_items', 'estimate_hours')) {
                $table->decimal('estimate_hours', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_meeting_action_items', function (Blueprint $table) {
            $table->dropColumn('estimate_hours');
        });
        Schema::table('project_management_meeting_tasks', function (Blueprint $table) {
            $table->dropColumn(['decision', 'return_expectation', 'revision_due_date', 'decided_by', 'decided_at']);
        });
    }
};
