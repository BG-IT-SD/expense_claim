<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sigfiles', function (Blueprint $table) {
            $table->id(); // id (PK)
            $table->string('empid', 20); // รหัสพนักงาน
            $table->string('path', 255)->nullable(); // ที่อยู่ไฟล์
            $table->string('etc', 100)->nullable(); // อื่นๆ
            $table->tinyInteger('status')->default(1); // 0=ไม่ใช้งาน, 1=ใช้งาน
            $table->tinyInteger('deleted')->default(0); // 0=ไม่ลบ, 1=ลบ
            $table->unsignedBigInteger('created_by')->nullable(); // user ที่สร้าง
            $table->timestamp('created_at')->nullable(); // เวลาที่สร้าง
            $table->unsignedBigInteger('modified_by')->nullable(); // user ที่แก้ไขล่าสุด
            $table->timestamp('updated_at')->nullable(); // เวลาที่แก้ไขล่าสุด
        });
    }

    public function down()
    {
        Schema::dropIfExists('sigfiles');
    }
};
