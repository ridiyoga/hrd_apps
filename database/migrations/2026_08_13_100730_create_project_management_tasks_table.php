<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_management_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('milestone_id')->nullable();
            $table->uuid('parent_task_id')->nullable();
            $table->string('task_code', 50)->nullable()->index();
            $table->string('title', 200);
            $table->string('type', 50)->default('task')->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->string('status', 40)->default('todo')->index();
            $table->string('reporter_id')->nullable()->index();
            $table->string('primary_assignee_id')->nullable()->index();
            $table->string('reviewer_id')->nullable()->index();
            $table->decimal('estimate_hours', 12, 2)->nullable();
            $table->decimal('actual_hours', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable()->index();
            $table->text('acceptance_criteria')->nullable();
            $table->text('blocker_reason')->nullable();
            $table->timestampTz('blocked_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('project_id')
                ->references('id')
                ->on('project_management_projects')
                ->cascadeOnDelete();
            $table->foreign('milestone_id')
                ->references('id')
                ->on('project_management_milestones')
                ->nullOnDelete();
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'priority']);
            $table->index('parent_task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_tasks');
    }
};
