<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom approval_group_id pada tabel ticket_hub_menus
     * agar setiap menu hub bisa diikat ke satu ApprovalGroup tertentu.
     */
    public function up(): void
    {
        Schema::table('ticket_hub_menus', function (Blueprint $table) {
            $table->string('approval_group_id', 36)->nullable()->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_hub_menus', function (Blueprint $table) {
            $table->dropColumn('approval_group_id');
        });
    }
};
