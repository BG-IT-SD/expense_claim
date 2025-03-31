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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('prefix',10);
            $table->integer('bookid');
            $table->string('empid',20);
            $table->tinyInteger('extype');
            $table->tinyInteger('departurefrom')->nullable();
            // departureplant
            $table->string('departuretext',255)->nullable();
            $table->tinyInteger('returnfrom')->nullable();
             // returnplant
            $table->string('returnfromtext',255)->nullable();
            $table->time('returntime');
            $table->decimal('totaldistance', 10, 2);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->tinyInteger('checktoil');
            // fuel91id
            // fuelpricesid
            $table->decimal('publictransportfare', 10, 2)->nullable();
            $table->decimal('expresswaytoll', 10, 2)->nullable();
            $table->decimal('otherexpenses', 10, 2)->nullable();
            $table->decimal('costoffood', 10, 2);
            $table->decimal('travelexpenses', 10, 2);
            $table->decimal('gasolinecost', 10, 2);
            $table->decimal('totalprice', 10, 2);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('expensefoods', function (Blueprint $table) {
            $table->id();
            // exid
            //mealid
            $table->float('meal1', 10, 2)->default(0.00)->nullable();
            $table->float('meal2', 10, 2)->default(0.00)->nullable();
            $table->float('meal3', 10, 2)->default(0.00)->nullable();
            $table->float('meal4', 10, 2)->default(0.00)->nullable();
            $table->tinyInteger('meal1reject')->default(0);
            $table->tinyInteger('meal2reject')->default(0);
            $table->tinyInteger('meal3reject')->default(0);
            $table->tinyInteger('meal4reject')->default(0);
            $table->float('totalpricebf', 10, 2)->default(0.00);
            $table->float('totalreject', 10, 2)->default(0.00);
            $table->float('totalprice', 10, 2)->default(0.00);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('expensefiles', function (Blueprint $table) {
            $table->id();
            // exid
            $table->string('path',255);
            $table->string('etc',100);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('approve', function (Blueprint $table) {
            $table->id();
            // exid
            $table->string('path',255);
            $table->tinyInteger('typeapprove');
            $table->string('empid',20);
            $table->tinyInteger('statusappprove');
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
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expensefoods');
        Schema::dropIfExists('expensefiles');
    }
};
