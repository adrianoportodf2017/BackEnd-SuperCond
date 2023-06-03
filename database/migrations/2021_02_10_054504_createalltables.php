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
        
        Schema::create('condominios', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('codigo')->unique();
            $table->string('cnpj')->unique()->nullable();;
            $table->string('adress')->nullable();;
            $table->string('city')->nullable();;
            $table->string('zip_code')->nullable();;
            $table->string('adress_billit')->nullable();;
            $table->string('description')->nullable();;
            $table->string('thumb')->nullable();;
        });
        
        Schema::create('users', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('profile');
            $table->string('password');
        });

        Schema::create('units', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('id_owner');
            $table->integer('id_condominio');
        });

        Schema::create('unitpeoples', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->date('birthdate');
        });

        Schema::create('unitvehicles', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('color');
            $table->string('plate');
        });

        Schema::create('unitpets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->string('race');
        });

        Schema::create('walls', function(Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->datetime('datecreated');
            $table->integer('id_condominio');

        });

        Schema::create('walllikes', function(Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
            $table->integer('id_condominio');

        });

        Schema::create('docs', function(Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fileurl');
            $table->datetime('datecreated');
            $table->integer('id_condominio');

        });

        Schema::create('billets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('fileurl');
            $table->integer('id_condominio');

        });

        Schema::create('warnings', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('status')->default('IN_REVIEW'); // IN_REVIEW, RESOLVED
            $table->date('datecreated');
            $table->text('photos');
            $table->integer('id_condominio');

        });

        Schema::create('foundandlost', function(Blueprint $table) {
            $table->id();
            $table->string('status')->default('LOST');  // LOST, RECOVERED
            $table->string('photo');
            $table->string('description');
            $table->string('where');
            $table->date('datecreated');
            $table->integer('id_condominio');

        });

        Schema::create('areas', function(Blueprint $table) {
            $table->id();
            $table->integer('allowed')->default(1);
            $table->string('title');
            $table->string('cover');
            $table->string('days'); 
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('id_condominio');

        });

        Schema::create('areadisableddays', function(Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day');
            $table->integer('id_condominio');

        });

        Schema::create('reservations', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_area');
            $table->datetime('reservation_date');
            $table->integer('id_condominio');

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condominios');
        Schema::dropIfExists('users');
        Schema::dropIfExists('units');
        Schema::dropIfExists('unitpeoples');
        Schema::dropIfExists('unitvehicles');
        Schema::dropIfExists('unitpets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('walllikes');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('foundandlost');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('areadisableddays');
        Schema::dropIfExists('reservations');
    }
}
