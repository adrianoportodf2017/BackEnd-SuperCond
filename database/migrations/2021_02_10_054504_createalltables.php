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
        
        Schema::create('condominios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('codigo')->unique();
            $table->string('cnpj')->unique()->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('address_billit')->nullable();
            $table->text('description')->nullable();
            $table->string('thumb')->nullable();
            // Adicione as colunas ausentes aqui
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('billit')->nullable();
        });
        
        
        Schema::create('users', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('cpf')->unique();
            $table->string('profile')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
                  });

        Schema::create('units', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('id_owner');
            $table->integer('id_condominio')->nullable();
        });

        Schema::create('unitpeoples', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->date('birthdate')->nullable();
        });

        Schema::create('unitvehicles', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('color')->nullable();
            $table->string('plate');
        });

        Schema::create('unitpets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->string('race')->nullable();
        });

        Schema::create('walls', function(Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->datetime('datecreated');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('walllikes', function(Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fileurl');
            $table->datetime('datecreated');
            $table->integer('id_condominio')->nullable();
            // Adicione as colunas ausentes aqui
            $table->string('filename')->nullable();
        });

        Schema::create('billets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('fileurl');
            $table->string('content')->nullable();
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('warnings', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('status')->default('IN_REVIEW'); // IN_REVIEW, RESOLVED
            $table->date('datecreated');
            $table->text('photos')->nullable();
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('foundandlost', function(Blueprint $table) {
            $table->id();
            $table->string('status')->default('LOST');  // LOST, RECOVERED
            $table->text('photos');
            $table->string('description');
            $table->string('where');
            $table->date('datecreated');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('areas', function(Blueprint $table) {
            $table->id();
            $table->integer('allowed')->default(1);
            $table->string('title');
            $table->string('cover');
            $table->string('days'); 
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('areadisableddays', function(Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('reservations', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_area');
            $table->datetime('reservation_date');
            $table->integer('id_condominio')->nullable();

        });

        Schema::create('assembleias', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->enum('status', ['1', '0'])->default('1');
            $table->integer('order')->nullable();
            $table->string('year')->nullable();
            $table->string('thumb')->nullable();
            $table->timestamps();
        });

        Schema::create('assembleiadocuments', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('thumb')->nullable();
            $table->text('content')->nullable();
            $table->enum('status', ['1', '0'])->default('1');
            $table->string('file_url')->nullable();
            $table->unsignedBigInteger('assembleia_id')->nullable();
            $table->timestamps();

            
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
        Schema::dropIfExists('assembleiadocuments');
        Schema::dropIfExists('assembleias');


    }
}
