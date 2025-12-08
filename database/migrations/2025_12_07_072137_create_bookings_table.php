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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('amount_time');
            $table->boolean('is_event')->default(false);
            $table->uuid('ground_id');
            $table->string('target', 255)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('owner_note')->nullable();
            $table->integer('quantity')->default(30);
            $table->string('status');
            $table->uuid('event_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ground_id')->references('id')->on('grounds')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
