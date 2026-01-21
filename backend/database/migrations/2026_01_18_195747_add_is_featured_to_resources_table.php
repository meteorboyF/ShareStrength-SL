<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('resources')) {
            return;
        }

        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('resources')) {
            return;
        }

        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};
