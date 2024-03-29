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
            $table->timestamps();
        });


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('cpf')->unique();
            $table->string('profile')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });


    


        // Índices
   

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('roles')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });


        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->integer('owner_id');
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('unit_peoples', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id');
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('unit_vehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id');
            $table->string('title');
            $table->string('color')->nullable();
            $table->string('plate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('unit_pets', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id');
            $table->string('name');
            $table->string('race')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('content');
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();
            $table->string('status')->nullable();
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('wall_likes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fileurl');
            $table->string('content')->nullable();
            $table->integer('condominio_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable(); // Chave estrangeira para categoria
            // Adicione as colunas ausentes aqui
            $table->string('filename')->nullable();
            $table->timestamps();
        });

        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('status')->nullable();
            $table->text('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();
            $table->string('parent_id')->nullable(); // Chave estrangeira para a pasta pai.
            $table->timestamps();
        });

        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('price')->nullable();
            $table->string('date_vue')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('date_payment')->nullable();
            $table->string('status')->nullable();
            $table->string('fileurl')->nullable();
            $table->string('filename')->nullable();
            $table->timestamps();
        });

        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('notes')->nullable();
            $table->string('unit_id')->nullable();
            $table->string('owner_id')->nullable();
            $table->string('status')->default('IN_REVIEW'); // IN_REVIEW, RESOLVED
            $table->text('photos')->nullable()
                /**Aqui podemos colocar varias fotos, serão urls finais, apois o upload na pasta storage*/
            ;
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('lost_end_found', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->string('status')->default('LOST');  // LOST, RECOVERED
            $table->text('content')->nullable();
            $table->string('where')->nullable();
            $table->text('notes')->nullable();
            $table->string('owner_id')->nullable();
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->integer('allowed')->default(1);
            $table->string('title');
            $table->string('days');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('area_disabled_days', function (Blueprint $table) {
            $table->id();
            $table->integer('area_id');
            $table->date('day');
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id');
            $table->integer('area_id');
            $table->datetime('reservation_date');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->integer('condominio_id')->nullable();
            $table->timestamps();
        });

        Schema::create('assembleias', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('content')->nullable(); // Usando longText
            $table->enum('status', ['1', '0'])->default('1');
            $table->integer('order')->nullable();
            $table->string('year')->nullable();
            $table->string('thumb')->nullable();
            $table->timestamps();
        });

        Schema::create('assembleia_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('thumb')->nullable();
            $table->text('content')->nullable();
            $table->enum('status', ['1', '0'])->default('1');
            $table->string('file_url')->nullable();
            $table->unsignedBigInteger('assembleia_id')->nullable();
            $table->timestamps();
        });

        Schema::create('classifieds', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('price')->nullable(); // Decimal para preço com duas casas decimais
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('status')->nullable()->default('NÃO VENDIDO');;
            $table->string('user_id')->nullable(); // Chave estrangeira para usuário
            $table->string('category_id')->nullable(); // Chave estrangeira para categoria
            $table->timestamps();
        });


        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('slug')->nullable(); // Campo único para URLs amigáveis
            $table->string('category_id')->nullable(); // Chave estrangeira para categoria
            $table->string('comments_count')->nullable(); // Contador de comentários
            $table->string('likes_count')->nullable(); // Contador de curtidas
            $table->string('views_count')->nullable(); // Contador de visualizações
            $table->string('author_id')->nullable(); // Chave estrangeira para autor
            $table->string('tags')->nullable(); // Tags ou categorias adicionais
            $table->string('highlight')->nullable(); // Destaque
            $table->string('status')->default('published'); // Status (publicada, rascunho, arquivada, etc.)
            $table->json('additional_images')->nullable(); // Imagens adicionais em formato JSON
            $table->string('external_url')->nullable(); // URL externa, se aplicável
            $table->string('shares_count')->default('0')->nullable(); // Contador de compartilhamentos
            $table->timestamps();
        });

        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('published')->nullable(); // Status (publicada, rascunho, arquivada, etc.)
            $table->integer('likes_count')->default(0); // Contador de curtidas
            $table->integer('comments_count')->default(0); // Contador de comentários
            $table->string('tags')->nullable(); // Tags ou categorias adicionais
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();
            $table->integer('shares_count')->default(0); // Contador de compartilhamentos
            $table->timestamps();
        });


        Schema::create('midias', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('content')->nullable();
            $table->string('url')->nullable();
            $table->string('file')->nullable();
            $table->string('type')->nullable();
            $table->string('user_id')->nullable();
            $table->string('status')->default('ativo'); // Status (publicada, rascunho, arquivada, etc.)
            $table->unsignedBigInteger('mediable_id')->nullable();
            $table->string('mediable_type')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('thumb_url')->nullable();
            $table->string('slug')->nullable();
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('active');
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();
            // Status (ativo, inativo, encerrado, etc.)
            $table->string('date_start')->nullable(); // Data de início da enquete
            $table->string('date_expiration')->nullable(); // Data de expiração da enquete
            $table->string('type')->default('single_choice')->nullable(); // Tipo de enquete (escolha única, múltipla escolha, etc.)
            $table->string('likes_count')->default(0); // Contador de curtidas
            $table->string('votes_count')->default(0); // Contador de votos totais
            $table->string('max_votes')->nullable(); // Número máximo de votos permitidos por usuário
            $table->boolean('is_public')->default(true); // Indicador de se a enquete é pública ou privada
            $table->json('participants')->nullable(); // Lista de usuários que participaram da enquete (pode ser uma lista de IDs de usuário em formato JSON)
            $table->timestamps();
        });

        Schema::create('questions_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('type')->nullable();
            /**  Text, chekbox*/
            $table->string('poll_id')->nullable();
            $table->timestamps();
        });

        Schema::create('votes_polls', function (Blueprint $table) {
            $table->id();
            $table->text('answer')->nullable();
            $table->string('question_poll_id')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('service_type')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('website')->nullable(); // Adicionado campo para website
            $table->string('social_media')->nullable(); // Adicionado campo para redes sociais
            $table->string('work_hours')->nullable(); // Adicionado campo para horário de trabalho
            $table->string('availability')->nullable(); // Adicionado campo para disponibilidade
            $table->float('average_rating')->nullable(); // Adicionado campo para média de avaliações
            $table->integer('total_ratings')->nullable(); // Adicionado campo para total de avaliações
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();


            $table->timestamps();
        });



        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('service_type')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('website')->nullable(); // Adicionado campo para website
            $table->string('social_media')->nullable(); // Adicionado campo para redes sociais
            $table->string('work_hours')->nullable(); // Adicionado campo para horário de trabalho
            $table->string('availability')->nullable(); // Adicionado campo para disponibilidade
            $table->float('average_rating')->nullable(); // Adicionado campo para média de avaliações
            $table->integer('total_ratings')->nullable(); // Adicionado campo para total de avaliações
            $table->string('thumb')->nullable();
            $table->string('thumb_file')->nullable();
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
        Schema::dropIfExists('unit_peoples');
        Schema::dropIfExists('unit_vehicles');
        Schema::dropIfExists('unit_pets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('wall_likes');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('lost_end_found');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('area_disabled_days');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('assembleia_documents');
        Schema::dropIfExists('assembleias');
        Schema::dropIfExists('classifieds');
        Schema::dropIfExists('news');
        Schema::dropIfExists('gallery');
        Schema::dropIfExists('midias');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('service_providers');
        Schema::dropIfExists('benefits');
    }
}
