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
        Schema::create('distance_rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('startplant');
            $table->unsignedBigInteger('endplant');

            $table->decimal('kilometer', 10, 2);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('startplant')
                ->references('id')->on('plants')
                ->onDelete('cascade');

            $table->foreign('endplant')
                ->references('id')->on('plants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distance_rates');
    }
};
