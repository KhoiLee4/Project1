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
        Schema::create('venues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 200);
            $table->string('sub_address', 100);
            $table->string('district', 100);
            $table->string('city', 100);
            $table->string('address', 300);
            $table->string('operating_time', 100);
            $table->string('phone_number1', 20)->unique();
            $table->string('phone_number2', 20)->nullable()->unique();
            $table->string('website', 255)->nullable();
            $table->decimal('deposit', 18, 2)->default(0);
            $table->uuid('owner_id');
            $table->timestamps();
            
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
