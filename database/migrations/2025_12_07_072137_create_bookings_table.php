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
            
            // CÁC CỘT CHO ĐẶT SÂN THƯỜNG (Để NULLABLE vì đặt Event không cần)
            $table->date('date')->nullable(); 
            $table->time('start_time')->nullable(); 
            $table->time('end_time')->nullable(); 
            $table->integer('amount_time')->nullable(); // Số giờ chơi
            $table->uuid('ground_id')->nullable();      // Đặt sự kiện thì không cần chọn sân con cụ thể
            
            // CÁC CỘT CHUNG
            $table->boolean('is_event')->default(false);
            $table->integer('quantity')->default(1); // Số lượng (giờ chơi hoặc vé)
            $table->decimal('total_price', 18, 2)->default(0); // THÊM: Tổng tiền thanh toán
            
            $table->string('target', 255)->nullable(); // Đối tượng (Sinh viên/Người lớn)
            $table->text('customer_note')->nullable();
            $table->text('owner_note')->nullable();
            $table->string('status'); // Pending, Confirmed, Cancelled...
            
            // CỘT CHO ĐẶT SỰ KIỆN
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
