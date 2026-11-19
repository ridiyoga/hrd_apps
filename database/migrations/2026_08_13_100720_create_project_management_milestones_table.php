<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_management_milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('milestone_name', 200);
            $table->string('owner_id')->nullable()->index();
            $table->date('target_date')->nullable();
            $table->string('status', 40)->default('planned')->index();
            $table->text('success_condition')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('project_id')
                ->references('id')
                ->on('project_management_projects')
                ->cascadeOnDelete();
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_milestones');
    }
};
