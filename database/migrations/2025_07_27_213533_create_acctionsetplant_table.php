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
        Schema::create('accountsetplanthead', function (Blueprint $table) {
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

        Schema::create('accountplant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('headid');
            $table->foreign('headid')
                ->references('id')
                ->on('accountsetplanthead')
                ->onDelete('cascade');
            $table->unsignedBigInteger('plantid');
            $table->foreign('plantid')
                ->references('id')
                ->on('plants')
                ->onDelete('cascade');
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1); // 0=ไม่ใช้งาน, 1=ใช้งาน
            $table->tinyInteger('deleted')->default(0); // 0=ไม่ลบ, 1=ลบ
            $table->unsignedBigInteger('created_by')->nullable(); // user ที่สร้าง
            $table->timestamp('created_at')->nullable(); // เวลาที่สร้าง
            $table->unsignedBigInteger('modified_by')->nullable(); // user ที่แก้ไขล่าสุด
            $table->timestamp('updated_at')->nullable(); // เวลาที่แก้ไขล่าสุด
        });

        Schema::create('accountstep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('headid');
            $table->foreign('headid')
                ->references('id')
                ->on('accountsetplanthead')
                ->onDelete('cascade');
            $table->tinyInteger('step');
            $table->string('empid', 20);
            $table->string('email', 100);
            $table->string('fullname', 255);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accountsetplanthead');
        Schema::dropIfExists('accountplant');
        Schema::dropIfExists('accountstep');
    }
};
