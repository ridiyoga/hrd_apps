<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dokumen_legal_reminder_logs')) {
            return;
        }

        Schema::create('dokumen_legal_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('dokumen_id')->index();
            $table->string('recipient_user_id')->nullable()->index();
            $table->string('recipient_email')->index();
            $table->string('reminder_type', 20)->index();
            $table->date('target_date')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['dokumen_id', 'recipient_email', 'reminder_type'], 'dokumen_legal_reminder_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_legal_reminder_logs');
    }
};
