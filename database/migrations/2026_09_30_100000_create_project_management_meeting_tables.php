<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_management_meetings')) {
            Schema::create('project_management_meetings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('project_id');
                $table->uuid('milestone_id')->nullable();
                $table->string('meeting_code', 50)->unique();
                $table->string('meeting_type', 30)->index();
                $table->string('title', 200);
                $table->timestampTz('meeting_at')->index();
                $table->string('location', 255)->nullable();
                $table->text('agenda');
                $table->text('discussion_notes')->nullable();
                $table->text('decisions')->nullable();
                $table->text('management_support')->nullable();
                $table->string('created_by');
                $table->string('updated_by')->nullable();
                $table->timestampsTz();
                $table->softDeletesTz();
                $table->foreign('project_id')->references('id')->on('project_management_projects')->cascadeOnDelete();
                $table->foreign('milestone_id')->references('id')->on('project_management_milestones')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('project_management_meeting_participants')) {
            Schema::create('project_management_meeting_participants', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('meeting_id');
                $table->string('user_id')->nullable()->index();
                $table->string('participant_name', 200);
                $table->string('participant_role', 100)->nullable();
                $table->string('attendance_status', 20)->default('present');
                $table->timestampsTz();
                $table->foreign('meeting_id')->references('id')->on('project_management_meetings')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('project_management_meeting_tasks')) {
            Schema::create('project_management_meeting_tasks', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('meeting_id');
                $table->uuid('task_id');
                $table->text('discussion_notes')->nullable();
                $table->timestampsTz();
                $table->foreign('meeting_id')->references('id')->on('project_management_meetings')->cascadeOnDelete();
                $table->foreign('task_id')->references('id')->on('project_management_tasks')->cascadeOnDelete();
                $table->unique(['meeting_id', 'task_id']);
            });
        }

        if (!Schema::hasTable('project_management_meeting_action_items')) {
            Schema::create('project_management_meeting_action_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('meeting_id');
                $table->uuid('linked_task_id')->nullable();
                $table->uuid('generated_task_id')->nullable();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('pic_id')->nullable()->index();
                $table->date('due_date')->nullable();
                $table->string('status', 30)->default('open')->index();
                $table->string('created_by');
                $table->string('updated_by')->nullable();
                $table->timestampsTz();
                $table->foreign('meeting_id')->references('id')->on('project_management_meetings')->cascadeOnDelete();
                $table->foreign('linked_task_id')->references('id')->on('project_management_tasks')->nullOnDelete();
                $table->foreign('generated_task_id')->references('id')->on('project_management_tasks')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('project_management_meeting_attachments')) {
            Schema::create('project_management_meeting_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('meeting_id');
                $table->string('uploaded_by');
                $table->string('original_name');
                $table->string('file_path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->timestampsTz();
                $table->foreign('meeting_id')->references('id')->on('project_management_meetings')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_meeting_attachments');
        Schema::dropIfExists('project_management_meeting_action_items');
        Schema::dropIfExists('project_management_meeting_tasks');
        Schema::dropIfExists('project_management_meeting_participants');
        Schema::dropIfExists('project_management_meetings');
    }
};
