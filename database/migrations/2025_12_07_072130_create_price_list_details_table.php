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
        Schema::create('price_list_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date')->nullable();
            $table->string('day', 20)->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 18, 2)->nullable();
            $table->decimal('fixed_price', 18, 2)->nullable();
            $table->decimal('current_price', 18, 2);
            $table->uuid('price_list_id');
            $table->timestamps();
            
            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_details');
    }
};
