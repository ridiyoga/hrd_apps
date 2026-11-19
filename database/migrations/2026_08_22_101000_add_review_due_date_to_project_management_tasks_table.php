<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('project_management_tasks', 'review_due_date')) {
                $table->date('review_due_date')->nullable()->after('due_revision_date')->index();
            }
        });

        DB::table('project_management_tasks')
            ->where('status', 'review')
            ->whereNull('review_due_date')
            ->whereNotNull('status_updated_at')
            ->get(['id', 'status_updated_at'])
            ->each(function ($task) {
                DB::table('project_management_tasks')
                    ->where('id', $task->id)
                    ->update([
                        'review_due_date' => $this->addWorkingDays($task->status_updated_at, 3)->toDateString(),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('project_management_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('project_management_tasks', 'review_due_date')) {
                $table->dropColumn('review_due_date');
            }
        });
    }

    private function addWorkingDays(mixed $date, int $days): Carbon
    {
        $targetDate = Carbon::parse($date)->startOfDay();

        for ($addedDays = 0; $addedDays < $days;) {
            $targetDate->addDay();

            if (!$targetDate->isSunday()) {
                $addedDays++;
            }
        }

        return $targetDate;
    }
};
