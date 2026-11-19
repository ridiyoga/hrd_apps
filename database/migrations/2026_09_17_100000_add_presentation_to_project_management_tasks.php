<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            $table->timestamp('presented_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            $table->dropColumn('presented_at');
        });
    }
};
