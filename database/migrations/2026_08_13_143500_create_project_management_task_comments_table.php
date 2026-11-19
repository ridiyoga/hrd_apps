<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_management_task_comments')) {
            return;
        }

        Schema::create('project_management_task_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id')->index();
            $table->string('user_id')->nullable()->index();
            $table->string('comment_type', 30)->default('comment')->index();
            $table->text('comment');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('task_id')
                ->references('id')
                ->on('project_management_tasks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_task_comments');
    }
};
