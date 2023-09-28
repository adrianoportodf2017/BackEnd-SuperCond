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
        
        
        Schema::create('users', function(Blueprint $table) {
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

        Schema::create('units', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->integer('id_owner');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();

        });

        Schema::create('unitpeoples', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

        });

        Schema::create('unitvehicles', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('color')->nullable();
            $table->string('plate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

        });

        Schema::create('unitpets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->string('race')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

        });

        Schema::create('walls', function(Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('content');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


        });

        Schema::create('walllikes', function(Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


        });

        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fileurl');
            $table->integer('id_condominio')->nullable();
            // Adicione as colunas ausentes aqui
            $table->string('filename')->nullable();
            $table->timestamps();

        });

        Schema::create('billets', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('fileurl');
            $table->string('content')->nullable();
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


        });

        Schema::create('warnings', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('status')->default('IN_REVIEW'); // IN_REVIEW, RESOLVED
            $table->text('photos')->nullable();
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


        });

        Schema::create('foundandlost', function(Blueprint $table) {
            $table->id();
            $table->string('status')->default('LOST');  // LOST, RECOVERED
            $table->text('photos');
            $table->string('description');
            $table->string('where');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


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
            $table->timestamps();


        });

        Schema::create('areadisableddays', function(Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


        });

        Schema::create('reservations', function(Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_area');
            $table->datetime('reservation_date');
            $table->integer('id_condominio')->nullable();
            $table->timestamps();


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

        Schema::create('classifieds', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('photos')->nullable(); // Usar JSON para armazenar várias fotos
            $table->string('price')->nullable(); // Decimal para preço com duas casas decimais
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Chave estrangeira para usuário
            $table->unsignedBigInteger('category_id')->nullable(); // Chave estrangeira para categoria
            $table->timestamps();
        });


        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('thumb')->nullable();
            $table->string('slug')->unique(); // Campo único para URLs amigáveis
            $table->unsignedBigInteger('category_id')->nullable(); // Chave estrangeira para categoria
            $table->string('comments_count')->default(0); // Contador de comentários
            $table->string('likes_count')->default(0); // Contador de curtidas
            $table->string('views_count')->default(0); // Contador de visualizações
            $table->unsignedBigInteger('author_id')->nullable(); // Chave estrangeira para autor
            $table->date('publish_date')->nullable(); // Data de publicação
            $table->string('tags')->nullable(); // Tags ou categorias adicionais
            $table->string('highlight')->default('0'); // Destaque
            $table->string('status')->default('published'); // Status (publicada, rascunho, arquivada, etc.)
            $table->json('additional_images')->nullable(); // Imagens adicionais em formato JSON
            $table->string('external_url')->nullable(); // URL externa, se aplicável
            $table->string('shares_count')->default('0'); // Contador de compartilhamentos
            $table->timestamps();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('published'); // Status (publicada, rascunho, arquivada, etc.)
            $table->integer('likes_count')->default(0); // Contador de curtidas
            $table->integer('comments_count')->default(0); // Contador de comentários
            $table->string('tags')->nullable(); // Tags ou categorias adicionais
            $table->string('thumb')->nullable();
            $table->json('photos')->nullable(); // Usar JSON para armazenar várias fotos
            $table->integer('shares_count')->default(0); // Contador de compartilhamentos
            $table->timestamps();
        });

        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('active');
            $table->string('thumb')->nullable();
            // Status (ativo, inativo, encerrado, etc.)
            $table->string('date_start')->nullable(); // Data de início da enquete
            $table->string('date_expiration')->nullable(); // Data de expiração da enquete
            $table->string('type')->default('single_choice'); // Tipo de enquete (escolha única, múltipla escolha, etc.)
            $table->json('options')->nullable(); // Opções de resposta em formato JSON
            $table->string('likes_count')->default(0); // Contador de curtidas
            $table->string('votes_count')->default(0); // Contador de votos totais
            $table->string('max_votes')->nullable(); // Número máximo de votos permitidos por usuário
            $table->boolean('is_public')->default(true); // Indicador de se a enquete é pública ou privada
            $table->json('participants')->nullable(); // Lista de usuários que participaram da enquete (pode ser uma lista de IDs de usuário em formato JSON)
            $table->timestamps();
        });

        Schema::create('serviceproviders', function (Blueprint $table) {
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
        Schema::dropIfExists('classifieds');
        Schema::dropIfExists('news');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('serviceproviders');







    }
}
