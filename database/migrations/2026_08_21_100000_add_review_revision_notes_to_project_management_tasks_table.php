<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_tasks', 'review_revision_notes')) {
                $table->text('review_revision_notes')->nullable()->after('review_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('project_management_tasks', 'review_revision_notes')) {
                $table->dropColumn('review_revision_notes');
            }
        });
    }
};
