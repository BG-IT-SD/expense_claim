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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');            // เช่น create, update, delete
            $table->string('model')->nullable(); // ชื่อโมเดล
            $table->text('description')->nullable(); // รายละเอียด
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
