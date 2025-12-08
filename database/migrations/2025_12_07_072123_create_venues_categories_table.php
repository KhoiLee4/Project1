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
        Schema::create('venues_categories', function (Blueprint $table) {
            $table->uuid('venue_id');
            $table->uuid('category_id');
            $table->timestamps();
            
            $table->primary(['venue_id', 'category_id']);
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues_categories');
    }
};
