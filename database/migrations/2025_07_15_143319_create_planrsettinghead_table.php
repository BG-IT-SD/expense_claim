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
        Schema::create('plantsettinghead', function (Blueprint $table) {
            $table->id();
            $table->string('headname', 20);
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1); // 0=ไม่ใช้งาน, 1=ใช้งาน
            $table->tinyInteger('deleted')->default(0); // 0=ไม่ลบ, 1=ลบ
            $table->unsignedBigInteger('created_by')->nullable(); // user ที่สร้าง
            $table->timestamp('created_at')->nullable(); // เวลาที่สร้าง
            $table->unsignedBigInteger('modified_by')->nullable(); // user ที่แก้ไขล่าสุด
            $table->timestamp('updated_at')->nullable(); // เวลาที่แก้ไขล่าสุด
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantsettinghead');
    }
};
