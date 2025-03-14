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
        Schema::create('fuelprice91s', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dateprice');
            $table->float('price', 10, 2);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            // $table->dateTime('created_date');
            $table->integer('modified_by')->nullable();
            // $table->dateTime('modified_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuelprice91s');
    }
};
