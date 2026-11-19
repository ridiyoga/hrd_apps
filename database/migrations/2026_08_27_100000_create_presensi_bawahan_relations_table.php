<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('presensi_bawahan_relations')) {
            return;
        }

        Schema::create('presensi_bawahan_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('holding_id')->index();
            $table->uuid('atasan_id')->index();
            $table->uuid('bawahan_id')->index();
            $table->uuid('created_by')->nullable()->index();
            $table->timestamps();

            $table->unique(
                ['holding_id', 'atasan_id', 'bawahan_id'],
                'presensi_bawahan_unique_relation'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_bawahan_relations');
    }
};
