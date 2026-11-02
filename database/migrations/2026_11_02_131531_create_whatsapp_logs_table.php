<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->onDelete('cascade');
            $table->string('phone', 20);
            $table->enum('direction', ['masuk', 'keluar']);
            $table->text('message');
            $table->string('message_type')->default('text');
            $table->string('status')->default('terkirim');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
