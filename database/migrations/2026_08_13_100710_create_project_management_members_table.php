<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_management_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('user_id')->index();
            $table->string('project_role', 50)->default('developer');
            $table->decimal('allocation_percent', 5, 2)->default(0);
            $table->decimal('allocation_hours', 12, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('project_id')
                ->references('id')
                ->on('project_management_projects')
                ->cascadeOnDelete();
            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_management_members');
    }
};
