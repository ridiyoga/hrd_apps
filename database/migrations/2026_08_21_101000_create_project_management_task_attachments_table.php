<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_management_task_attachments')) {
            return;
        }

        Schema::create('project_management_task_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id')->index();
            $table->string('uploaded_by')->nullable()->index();
            $table->string('source', 50)->default('review')->index();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
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
        Schema::dropIfExists('project_management_task_attachments');
    }
};
