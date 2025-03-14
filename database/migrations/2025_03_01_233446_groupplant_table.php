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
        Schema::create('groupplants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mealid');
            $table->foreign('mealid')
                ->references('id')
                ->on('pricepermeals')
                ->onDelete('cascade');
            $table->unsignedBigInteger('plantid');
            $table->foreign('plantid')
                ->references('id')
                ->on('plants')
                ->onDelete('cascade');
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
        Schema::dropIfExists('groupplants');
    }
};
