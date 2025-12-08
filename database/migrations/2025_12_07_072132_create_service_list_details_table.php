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
        Schema::create('service_list_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('wholesale', 50)->nullable();
            $table->string('unit_wholesale', 50);
            $table->string('retail', 50);
            $table->string('unit_retail', 50);
            $table->uuid('service_list_id');
            $table->timestamps();
            
            $table->foreign('service_list_id')->references('id')->on('service_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_list_details');
    }
};
