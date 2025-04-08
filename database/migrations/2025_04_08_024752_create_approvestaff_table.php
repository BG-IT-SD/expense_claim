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
        Schema::create('approvestaff', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('extype'); // 1=ทั่วไป 2=พขร. 3=ช่าง
            $table->integer('step'); // ไม่มี (5)
            $table->integer('group'); // ไม่มี (5)
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
        Schema::dropIfExists('approvestaff');
    }
};
