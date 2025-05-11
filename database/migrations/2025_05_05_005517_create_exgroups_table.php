<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExgroupsTable extends Migration
{
    public function up()
    {
        Schema::create('exgroups', function (Blueprint $table) {
            $table->id();
            $table->date('groupdate'); // วันที่บันทึกกลุ่ม
            $table->time('grouptime'); // เวลาบันทึกกลุ่ม
            $table->tinyInteger('typeapprove');
            $table->tinyInteger('statusapprove');
            $table->string('checkempid', 20); // ผู้ตรวจสอบ
            $table->string('nextmpid', 20);
            $table->string('nextemail',255);
            $table->string('finalempid', 20);
            $table->string('finalemail',255);
            $table->string('accountempid', 20);
            $table->string('accountemail',255);

            // ค่าใช้จ่าย
            $table->decimal('totalfood', 10, 2)->nullable(); // ราคาค่าอาหารรวมกลุ่ม
            $table->decimal('totalfuel', 10, 2)->nullable(); // ราคาน้ำมันรวมกลุ่ม
            $table->decimal('publictransportfare', 10, 2)->nullable();
            $table->decimal('expresswaytoll', 10, 2)->nullable();
            $table->decimal('otherexpenses', 10, 2)->nullable();
            $table->decimal('totalother', 10, 2)->nullable(); // รายการอื่นรวมกลุ่ม
            $table->decimal('total', 10, 2)->nullable(); // รวมทั้งหมด

            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('exgroups');
    }
}

