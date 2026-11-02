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
        if (! Schema::hasColumn('users', 'bidang')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('bidang', 50)->nullable()->after('role');
            });
        }

        if (! Schema::hasColumn('categories', 'bidang')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('bidang', 50)->nullable()->after('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'bidang')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bidang');
            });
        }

        if (Schema::hasColumn('categories', 'bidang')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('bidang');
            });
        }
    }
};
