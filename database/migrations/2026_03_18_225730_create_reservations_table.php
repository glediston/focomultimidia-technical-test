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
        Schema::create('reservations', function (Blueprint $table) {
    $table->id(); // ID da reserva (XML)
    $table->foreignId('hotel_id')->constrained();
    $table->foreignId('room_id')->constrained();
    $table->string('customer_first_name');
    $table->string('customer_last_name');
    $table->date('arrival_date');
    $table->date('departure_date');
    $table->decimal('total_price', 10, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
