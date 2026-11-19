<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_management_task_activities')) {
            return;
        }

        Schema::create('project_management_task_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id')->index();
            $table->string('user_id')->nullable()->index();
            $table->string('activity_type', 50)->index();
            $table->string('field_name', 100)->nullable()->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description');
            $table->timestampsTz();

            $table->foreign('task_id')
                ->references('id')
                ->on('project_management_tasks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_task_activities');
    }
};
