<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 20)->unique();
            $table->string('reporter_name');
            $table->string('reporter_phone', 20);
            $table->string('reporter_address')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('title');
            $table->text('description');
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->enum('status', [
                'baru',
                'diproses',
                'diteruskan',
                'didisposisikan',
                'ditindaklanjuti',
                'selesai',
                'ditolak',
            ])->default('baru');
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('ticket_code');
            $table->index('status');
            $table->index('reporter_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
