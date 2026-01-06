<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('trusted_contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('trusted_contacts', 'relation')) {
                $table->string('relation')->after('contact_name')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('trusted_contacts', function (Blueprint $table) {
            $table->dropColumn('relation');
        });
    }
};
