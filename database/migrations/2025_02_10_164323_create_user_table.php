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


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('empid', 20)->unique();
            $table->string('password', 100);
            $table->string('email', 100);
            $table->string('fullname', 255);
            $table->string('bu', 50)->nullable();
            $table->string('dept', 150)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->string('created_by', 20);
            $table->string('modified_by', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('modulename', 100);
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('rolename', 100);
            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid');
            $table->foreign('userid')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('moduleid');
            $table->foreign('moduleid')
                ->references('id')
                ->on('modules')
                ->onDelete('cascade');
            $table->unsignedBigInteger('roleid');
            $table->foreign('roleid')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->string('etc', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamps();
        });

        // Schema::create('profiles', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('userid');
        //     $table->foreign('userid')
        //         ->references('id')
        //         ->on('users')
        //         ->onDelete('cascade');
        //     $table->string('fullname', 255);
        //     $table->string('account_number', 100)->nullable();
        //     $table->string('bu', 50)->nullable();
        //     $table->string('dept', 100)->nullable();
        //     $table->tinyInteger('status')->default(1);
        //     $table->tinyInteger('deleted')->default(0);
        //     $table->integer('created_by');
        //     $table->integer('modified_by')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('user_roles');
    }
};
