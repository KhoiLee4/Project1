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
        Schema::create('image_venus', function (Blueprint $table) {
            $table->uuid('venue_id');
            $table->uuid('image_id');
            $table->boolean('is_image')->default(true);
            $table->timestamps();
            
            $table->primary(['venue_id', 'image_id']);
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_venus');
    }
};
