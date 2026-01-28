<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to users table if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('email_verified_at');
            }
        });

        // Add columns to helpers table if they don't exist
        Schema::table('helpers', function (Blueprint $table) {
            if (!Schema::hasColumn('helpers', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('helpers', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('is_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_suspended');
        });

        Schema::table('helpers', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'is_suspended']);
        });
    }
};
