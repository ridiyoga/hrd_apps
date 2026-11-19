<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_meetings', 'status')) {
                $table->string('status', 20)->default('draft')->index();
            }
            if (!Schema::hasColumn('project_management_meetings', 'summary')) {
                $table->text('summary')->nullable();
            }
            if (!Schema::hasColumn('project_management_meetings', 'finalized_at')) {
                $table->timestampTz('finalized_at')->nullable();
            }
            if (!Schema::hasColumn('project_management_meetings', 'finalized_by')) {
                $table->string('finalized_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_meetings', function (Blueprint $table) {
            $table->dropColumn(['status', 'summary', 'finalized_at', 'finalized_by']);
        });
    }
};
