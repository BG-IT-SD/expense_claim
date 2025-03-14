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

        Schema::create('typegroups', function (Blueprint $table) {
            $table->id();
            $table->string('groupname', 50);
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            // $table->dateTime('created_date');
            $table->integer('modified_by')->nullable();
            // $table->dateTime('modified_date')->nullable();
            $table->timestamps();
        });

        Schema::create('group_specials', function (Blueprint $table) {
            $table->id();
            // Foreign key for typeid
            $table->unsignedBigInteger('typeid');
            $table->foreign('typeid')
                ->references('id')
                ->on('typegroups')
                ->onDelete('cascade');  // Automatically delete group_special if typeid is deleted
            $table->integer('empid');
            $table->string('fullname', 255);
            $table->string('position', 255)->nullable();
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
     * Reerse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_specials');
        Schema::dropIfExists('typegroups');
    }
};
