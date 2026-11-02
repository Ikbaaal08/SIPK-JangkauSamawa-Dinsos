<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->change();
        });

        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'super_admin']);

        DB::table('users')
            ->where('role', 'kepala_dinas')
            ->update(['role' => 'kadis']);

        DB::table('users')
            ->where('role', 'petugas_penerima')
            ->update(['role' => 'kabag']);

        DB::table('users')
            ->where('role', 'petugas_pelaksana')
            ->update(['role' => 'petugas']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'admin']);

        DB::table('users')
            ->where('role', 'kadis')
            ->update(['role' => 'kadis']);

        DB::table('users')
            ->where('role', 'kabag')
            ->update(['role' => 'petugas_penerima']);

        DB::table('users')
            ->where('role', 'petugas')
            ->update(['role' => 'petugas_pelaksana']);
    }
};
