<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_hub_menu_approvals', function (Blueprint $table) {
            $table->id();
            $table->uuid('hub_menu_id');
            $table->string('holding_id', 36);
            $table->string('approval_group_id', 36);
            $table->timestamps();

            // Unique constraint so one holding only has one approval group per hub menu
            $table->unique(['hub_menu_id', 'holding_id'], 'hub_menu_holding_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_hub_menu_approvals');
    }
};
