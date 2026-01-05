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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('venue_id')->nullable();
            
            $table->string('name', 100);
            
            $table->dateTime('start_date')->nullable(); 
            $table->dateTime('end_date')->nullable();
            
            $table->decimal('price', 18, 2); // Giá vé
            $table->integer('ticket_number')->default(1); // Tổng số vé
            $table->string('level', 255)->nullable();
            
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
