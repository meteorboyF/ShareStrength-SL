<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessibility_settings', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type', 32);
            $table->unsignedBigInteger('actor_id');
            $table->json('settings');
            $table->timestamps();

            $table->unique(['actor_type', 'actor_id']);
            $table->index(['actor_type', 'actor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessibility_settings');
    }
};
