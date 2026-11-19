<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_management_projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('holding_id')->index();
            $table->string('project_code', 30)->unique();
            $table->string('project_name', 200);
            $table->string('project_type', 100);
            $table->string('category', 100)->nullable();
            $table->text('description');
            $table->text('background')->nullable();
            $table->text('objective')->nullable();
            $table->text('expected_benefit')->nullable();
            $table->string('requester_id')->index();
            $table->string('requester_department_id')->nullable()->index();
            $table->string('business_owner_id')->nullable()->index();
            $table->string('project_manager_id')->nullable()->index();
            $table->string('technical_lead_id')->nullable()->index();
            $table->string('sponsor_id')->nullable()->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->string('status', 40)->default('draft')->index();
            $table->string('health', 20)->default('gray')->index();
            $table->timestampTz('requested_at')->nullable();
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->timestampTz('actual_start_at')->nullable();
            $table->timestampTz('actual_end_at')->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->text('scope_summary')->nullable();
            $table->text('in_scope')->nullable();
            $table->text('out_of_scope')->nullable();
            $table->text('success_criteria')->nullable();
            $table->decimal('estimated_hours', 12, 2)->nullable();
            $table->decimal('actual_hours', 12, 2)->default(0);
            $table->text('hold_reason')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['holding_id', 'status']);
            $table->index(['holding_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_projects');
    }
};
