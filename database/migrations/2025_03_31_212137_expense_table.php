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
            $table->string('id', 20)->primary();
            $table->string('prefix',10);
            $table->integer('bookid');
            $table->string('empid',20);
            $table->tinyInteger('extype');
            $table->tinyInteger('departurefrom')->nullable();
            // departureplant
            $table->unsignedBigInteger('departureplant');
            $table->foreign('departureplant')
                ->references('id')
                ->on('plants')
                ->onDelete('cascade');
            $table->string('departuretext',255)->nullable();
            $table->tinyInteger('returnfrom')->nullable();
             // returnplant
             $table->unsignedBigInteger('returnplant');
             $table->foreign('returnplant')
                 ->references('id')
                 ->on('plants')
                 ->onDelete('cascade');
            $table->string('returnfromtext',255)->nullable();
            $table->time('returntime');

            $table->decimal('afdistance', 10, 2);
            $table->decimal('totaldistance', 10, 2);
            $table->decimal('distancemore', 10, 2)->nullable();
            $table->decimal('basedistance', 10, 2);

            $table->text('distancenote')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->tinyInteger('checktoil')->nullable();
            // fuel91id
            $table->unsignedBigInteger('fuel91id')->nullable();
            $table->foreign('fuel91id')
                ->references('id')
                ->on('fuel_price91s')
                ->onDelete('cascade');
            // fuelpricesid
            $table->unsignedBigInteger('fuelpricesid')->nullable();
            $table->foreign('fuelpricesid')
                ->references('id')
                ->on('fuelprices')
                ->onDelete('cascade');
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
            $table->string('exid');
            $table->foreign('exid')
                ->references('id')
                ->on('expenses')
                ->onDelete('cascade');
            //mealid
            $table->unsignedBigInteger('mealid');
            $table->foreign('mealid')
                ->references('id')
                ->on('pricepermeals')
                ->onDelete('cascade');
            $table->decimal('meal1', 10, 2)->default(0.00)->nullable();
            $table->decimal('meal2', 10, 2)->default(0.00)->nullable();
            $table->decimal('meal3', 10, 2)->default(0.00)->nullable();
            $table->decimal('meal4', 10, 2)->default(0.00)->nullable();
            $table->tinyInteger('meal1reject')->default(0);
            $table->tinyInteger('meal2reject')->default(0);
            $table->tinyInteger('meal3reject')->default(0);
            $table->tinyInteger('meal4reject')->default(0);
            $table->date('used_date');
            $table->decimal('totalpricebf', 10, 2)->default(0.00);
            $table->decimal('totalreject', 10, 2)->default(0.00);
            $table->decimal('totalprice', 10, 2)->default(0.00);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('expensefiles', function (Blueprint $table) {
            $table->id();
            $table->string('exid');
            $table->foreign('exid')
                ->references('id')
                ->on('expenses')
                ->onDelete('cascade');
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
            $table->string('exid');
            $table->foreign('exid')
                ->references('id')
                ->on('expenses')
                ->onDelete('cascade');
            // $table->string('path',255);
            $table->tinyInteger('typeapprove');
            $table->string('empid',20);
            $table->string('email',255);
            $table->string('approvename',255);
            $table->tinyInteger('emailstatus')->nullable();
            $table->tinyInteger('statusapprove');
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
        Schema::dropIfExists('approve');
        Schema::dropIfExists('expensefiles');
        Schema::dropIfExists('expensefoods');
        Schema::dropIfExists('expenses');
    }
};
