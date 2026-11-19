<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_identities', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36);
            $table->string('issuer');
            $table->string('subject');
            $table->timestamps();
            $table->unique(['issuer', 'subject']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_identities');
    }
};