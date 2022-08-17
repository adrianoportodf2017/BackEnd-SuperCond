<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Createalltables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('password');
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // APTO 801, LOTE  8 QD 27
            $table->integer('id_owner');
        });

        Schema::create('unitspeoples', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit'); 
            $table->string('name');
            $table->date('birthdate');
        });

        Schema::create('unitsvehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit'); 
            $table->string('title');
            $table->string('color');
            $table->string('plate');
        });

        Schema::create('unitspets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit'); 
            $table->string('name');
            $table->string('race');
        });

        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->string('body');
            $table->dateTime('datecreated');
        });

        Schema::create('walllikes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wall'); 
            $table->integer('id_user');
        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->string('fileurl');
        });

        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit'); 
            $table->string('title');
            $table->string('fileurl');
        });

        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit'); 
            $table->string('title');
            $table->string('status')->default('IN_REVIEW'); //IN_REVIEW, RESOLVER
            $table->dateTime('datacreated');
            $table->text('photos');// foto1.jpg, foto2.jpg
        });

        Schema::create('foundandlost', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('LOST'); //LOST, RECOVERED
            $table->string('description');
            $table->string('where');
            $table->string('photos');// foto1.jpg, foto2.jpg
            $table->dateTime('datacreated');
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->integer('allowed')->default(1); //1, 0
            $table->string('title');
            $table->string('cover');
            $table->string('days');// 0,1,2,3,4,5,6
            $table->time('start_time');
            $table->time('end_time');
        });

        Schema::create('areadisableddays', function (Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day'); 
          
        });

        Schema::create('reservetions', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_area');
            $table->dateTime('reservation_date');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('units');
        Schema::dropIfExists('unitspeoples');
        Schema::dropIfExists('unitsvehicles');
        Schema::dropIfExists('unitspets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('walllikes');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('foundandlost');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('areadisableddays');
        Schema::dropIfExists('reservetions');

    }
}
