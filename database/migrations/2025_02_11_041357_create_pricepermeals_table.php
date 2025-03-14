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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('levelname', 100);
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('groupprices', function (Blueprint $table) {
            $table->id();
            $table->string('groupname', 100);
            $table->unsignedBigInteger('levelid');
            $table->foreign('levelid')
                ->references('id')
                ->on('levels')
                ->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('pricepermeals', function (Blueprint $table) {
            $table->id();
            // Foreign key for groupid
            $table->unsignedBigInteger('groupid');
            $table->foreign('groupid')
                ->references('id')
                ->on('groupprices')
                ->onDelete('cascade');  // Automatically delete groupprice if groupid is deleted

            $table->float('meal1', 10, 2);
            $table->float('meal2', 10, 2);
            $table->float('meal3', 10, 2);
            $table->float('meal4', 10, 2);
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
        Schema::dropIfExists('pricepermeals');
    }
};
