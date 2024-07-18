<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('price', 8, 2);
            $table->integer('total_rooms');  // プラン作成時に設定したroom_count
            $table->integer('booked_rooms')->default(0);  // 予約された部屋数
            $table->enum('status', ['available', 'few', 'unavailable'])->default('available');
            $table->index('date');
            $table->timestamps();
            $table->unique(['plan_id', 'room_type_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_slots');
    }
};
