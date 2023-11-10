<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;



use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();




        DB::table('users')->insert([
            'name' => 'Adriano Alves',
            'email' => 'adrianobr00@gmail.com',
            'cpf' => '12345678901', // adicionado um valor aleatório para a coluna cpf
            'profile' => 1,
            'password' => Hash::make('12345'),
        ]);

        DB::table('users')->insert([
            'name' => 'Rosana Torres',
            'email' => 'torres@gmail.com',
            'cpf' => '12345678912', // adicionado um valor aleatório para a coluna cpf
            'profile' => 1,
            'password' => Hash::make('12345'),
        ]);
        DB::table('users')->insert([
            'name' => 'João de Souza',
            'email' => 'joao@gmail.com',
            'cpf' => '12345678913', // adicionado um valor aleatório para a coluna cpf
            'profile' => 1,
            'password' => Hash::make('12345'),
        ]);
        DB::table('users')->insert([
            'name' => 'Maria de Oliveira',
            'email' => 'Maria@gmail.com',
            'cpf' => '12345678914', // adicionado um valor aleatório para a coluna cpf
            'profile' => 1,
            'password' => Hash::make('12345'),
        ]);

        DB::table('profiles')->insert([
            ['name' => 'Administrador', 'roles' => 'Administrador', 'status' => 'ativo'],
            ['name' => 'Síndico', 'roles' => 'sindico', 'status' => 'ativo'],
            ['name' => 'Conselheiro', 'roles' => 'conselheiro', 'status' => 'ativo'],
            ['name' => 'Morador', 'roles' => 'morador', 'status' => 'ativo'],
            ['name' => 'Porteiro', 'roles' => 'porteiro', 'status' => 'ativo'],
            ['name' => 'Zelador', 'roles' => 'zelador', 'status' => 'ativo'],
        ]);

        DB::table('condominios')->insert([
            'name' => 'Condomínio ABC',
            'codigo' => 'ABC12822783',
            'cnpj' => '11.531.111/0001-11',
            'address' => 'Rua ABC, 123',
            'city' => 'São Paulo',
            'zip_code' => '12345-678',
            'address_billit' => 'Rua ABC, 123',
            'description' => 'Condomínio de luxo',
            'thumb' => 'condominio-abc.jpg',
        ]);
        /* INSERINDO UNIDADES   */

        DB::table('units')->insert([
            'name' => 'APT 100',
            'owner_id' => 1,
            'condominio_id' => 1,
            'address' => $faker->address,
            'notes' => $faker->paragraph
        ]);
        DB::table('units')->insert([
            'name' => 'APT 101',
            'owner_id' => 1,
            'condominio_id' => 1,
            'address' => $faker->address,
            'notes' => $faker->paragraph


        ]);
        DB::table('units')->insert([
            'name' => 'APT 200',
            'owner_id' => '0',
            'condominio_id' => 1,
            'address' => $faker->address,
            'notes' => $faker->paragraph


        ]);
        DB::table('units')->insert([
            'name' => 'APT 201',
            'owner_id' => '0',
            'condominio_id' => 1,
            'address' => $faker->address,
            'notes' => $faker->paragraph


        ]);

        /*  INSERINDO AREAS */

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'days' => '1,2,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'condominio_id' => 1

        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00',
            'condominio_id' => 1

        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'days' => '4,5,6',
            'start_time' => '09:00:00',
            'end_time' => '22:00:00',
            'condominio_id' => 1

        ]);

        /*  INSERINDO AVISOS   */

        DB::table('walls')->insert([
            'title' => $faker->sentence,
            'content' => $faker->paragraph,
            'created_at' => $faker->dateTime,
            'condominio_id' => 1,
            'thumb' => '/images/noticias.png',


        ]);
        DB::table('walls')->insert([
            'title' => $faker->sentence,
            'content' => $faker->paragraph,
            'created_at' => $faker->dateTime,
            'condominio_id' => 1,
            'thumb' => '/images/noticias.png',


        ]);

        /*  INSERINDO Assebleias  */

        DB::table('assembleias')->insert([
            'title' => 'AGO 2023',
            'content' => $faker->paragraph,
            'created_at' => '2023-12-20 18:00:00',
            'created_at' => now(),
            'updated_at' => now(),

        ]);


        DB::table('assembleias')->insert([
            'title' => 'AGe 2022',
            'content' => $faker->paragraph,
            'created_at' => '2022-12-20 18:00:00',
            'created_at' => now(),
            'updated_at' => now(),

        ]);


        DB::table('classifieds')->insert([
            [
                'title' => 'Oferta 1',
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'price' => '100.00',
                'address' => 'Qd 01 Cj 09 Ouro Vermelho II',
                'contact' => '(61)98565-5689',
                'user_id' => 1, // ID do usuário relacionado
                'category_id' => 1, // ID da categoria relacionada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oferta 2',
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'price' => '50.00',
                'address' => 'Qd 05 Cj 05 Ouro Vermelho II',
                'contact' => '(61)98565-5689',
                'user_id' => 2, // ID do usuário relacionado
                'category_id' => 2, // ID da categoria relacionada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oferta 3',
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'price' => '50.00',
                'address' => 'Qd 03 Cj 05 Ouro Vermelho II',
                'contact' => '(61)98565-5689',
                'user_id' => 3, // ID do usuário relacionado
                'category_id' => 2, // ID da categoria relacionada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oferta 4',
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'price' => '50.00',
                'address' => 'Qd20 Cj 05 Ouro Vermelho II',
                'contact' => '(61)98565-5689',
                'user_id' => 4, // ID do usuário relacionado
                'category_id' => 2, // ID da categoria relacionada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Oferta 5',
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'price' => '50.00',
                'address' => 'Qd 14 Cj 05 Ouro Vermelho II',
                'contact' => '(61)98565-5689',
                'user_id' => 1, // ID do usuário relacionado
                'category_id' => 2, // ID da categoria relacionada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Adicione mais registros conforme necessário
        ]);

        // Exemplo de seed para notícias
        for ($i = 1; $i <= 10; $i++) {
            DB::table('news')->insert([
                'title' => 'Notícia ' . $i,
                'content' => $faker->paragraph,
                'thumb' => '/images/noticias.png',
                'slug' => 'noticia-' . $i,
                'category_id' => rand(1, 5), // Supondo que você tenha 5 categorias diferentes
                'comments_count' => rand(0, 100),
                'likes_count' => rand(0, 100),
                'views_count' => rand(100, 10000),
                'author_id' => rand(1, 10), // Supondo que você tenha 10 autores diferentes
                'tags' => 'tag' . rand(1, 5), // Supondo que você tenha 5 tags diferentes
                'highlight' => rand(0, 1),
                'status' => 'published',
                'external_url' => null,
                'shares_count' => rand(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            DB::table('categories')->insert([
                'name' => 'Categoria ' . $i,
                'type' => 'tipo' . rand(1, 5),
                'content' => 'tipo' . rand(1, 5), // Supondo que você tenha 5 tipos diferentes
                'thumb' => 'thumb-categoria-' . $i . '.jpg',
                'thumb_url' => 'http://example.com/thumb-url-' . $i,
                'slug' => 'categoria-' . $i,
                'status' => 'active', // ou 'inactive' conforme necessário
                'notes' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            DB::table('gallery')->insert([
                'title' => 'Título da Galeria ' . $i,
                'content' => 'Conteúdo da Galeria ' . $faker->paragraph,
                'status' => 'published',
                'likes_count' => rand(0, 100),
                'comments_count' => rand(0, 100),
                'tags' => 'tag' . rand(1, 5), // Supondo que você tenha 5 tags diferentes
                'thumb' => 'imagem-galeria-' . $i . '.jpg',
                'thumb_file' => 'imagem-galeria-' . $i . '.jpg',
                'shares_count' => rand(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        foreach (range(1, 10) as $index) {
            $startDate = $faker->dateTimeBetween('-30 days', '+30 days');
            $expirationDate = $faker->dateTimeBetween($startDate, '+60 days');
           $pollData = [
                'title' => 'Enquete ' . $index,
                'content' => $faker->paragraph,
                'status' => 'active',
                'date_start' => $startDate,
                'date_expiration' => $expirationDate,
                'type' => 'single_choice',
                'likes_count' => $faker->numberBetween(0, 100),
                'votes_count' => $faker->numberBetween(0, 1000),
                'max_votes' => $faker->numberBetween(1, 5), // Número máximo de votos permitidos por usuário
                'is_public' => true,
                'participants' => json_encode([]), // Lista de usuários que participaram da enquete
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('polls')->insert($pollData);
        }


        foreach (range(1, 10) as $index) {
            DB::table('questions_polls')->insert([
                'title' => $faker->sentence(4),
                'content' => $faker->paragraph(3),
                'type' => $faker->randomElement(['Text', 'Checkbox']),
                'poll_id' => $faker->numberBetween(1, 5), // Substitua pelo ID da enquete correspondente
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (range(1, 10) as $index) {
            DB::table('votes_polls')->insert([
                'answer' => $faker->sentence(6),
                'question_poll_id' => $faker->numberBetween(1, 10), // Substitua pelo ID da pergunta correspondente
                'user_id' => $faker->numberBetween(1, 20), // Substitua pelo ID do usuário correspondente
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        foreach (range(1, 10) as $index) {
            $serviceProviderData = [
                'name' => $faker->company,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'service_type' => $faker->word,
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'zip_code' => $faker->postcode,
                'website' => $faker->url, // Adicionado campo para website
                'social_media' => json_encode(['facebook' => $faker->url, 'instagram' => $faker->url]), // Adicionado campo para redes sociais
                'work_hours' => $faker->sentence, // Adicionado campo para horário de trabalho
                'availability' => $faker->sentence, // Adicionado campo para disponibilidade
                'average_rating' => $faker->randomFloat(1, 1, 5), // Adicionado campo para média de avaliações
                'total_ratings' => $faker->numberBetween(1, 100), // Adicionado campo para total de avaliações
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('service_providers')->insert($serviceProviderData);
        }


        foreach (range(1, 10) as $index) {
            $benefits = [
                'name' => $faker->company,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'service_type' => $faker->word,
                'description' => $faker->paragraph,
                'address' => $faker->address,
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'zip_code' => $faker->postcode,
                'website' => $faker->url, // Adicionado campo para website
                'social_media' => json_encode(['facebook' => $faker->url, 'instagram' => $faker->url]), // Adicionado campo para redes sociais
                'work_hours' => $faker->sentence, // Adicionado campo para horário de trabalho
                'availability' => $faker->sentence, // Adicionado campo para disponibilidade
                'average_rating' => $faker->randomFloat(1, 1, 5), // Adicionado campo para média de avaliações
                'total_ratings' => $faker->numberBetween(1, 100), // Adicionado campo para total de avaliações
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table('benefits')->insert($benefits);
        }

        $docs = [];
        for ($i = 0; $i < 10; $i++) {
            $docs[] = [
                'title' => $faker->sentence(5),
                'content' => $faker->paragraph(10),
                'category_id' => $faker->numberBetween(1, 5),
                'fileurl' => $faker->imageUrl(),
                'filename' => $faker->word(),
            ];
        }

        DB::table('docs')->insert($docs);




      
        foreach (range(1, 10) as $index) {
            DB::table('folders')->insert([
                'title' => $faker->sentence(4),
                'content' => $faker->paragraph(3),
                'parent_id' => $faker->numberBetween(1, 5),
                'thumb' => $faker->imageUrl(800, 600),
                'thumb_file' => $faker->imageUrl(800, 600),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
      


        $units = [];
        for ($i = 0; $i < 10; $i++) {
            $units[] = [
                'name' => $faker->name(),
                'owner_id' => $faker->numberBetween(1, 5),
                'address' => $faker->address(),
                'address' => $faker->address,
                'notes' => $faker->paragraph
            ];
        }

        DB::table('units')->insert($units);

        $bill = [];
        for ($i = 0; $i < 100; $i++) {
            $unit_id = $faker->numberBetween(1, 10);
            $owner_id = $faker->numberBetween(1, 10);

            $bill[] = [
                'title' => $faker->sentence(5),
                'content' => $faker->paragraph(10),
                'price' => $faker->randomFloat(2, 100, 1000),
                'date_vue' => $faker->date(),
                'unit_id' => $unit_id,
                'owner_id' => $owner_id,
                'date_payment' => $faker->date(),
                'status' => $faker->randomElement(['pago', 'pendente']),
                'fileurl' => $faker->imageUrl(800, 600),
                'filename' => $faker->imageUrl(800, 600),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('billets')->insert($bill);



        for ($i = 0; $i < 100; $i++) {
            $unit_id = $faker->numberBetween(1, 10);
            $owner_id = $faker->numberBetween(1, 10);
            $condominio_id = $faker->numberBetween(1, 10);

            $warning[] = [
                'title' => $faker->sentence(5),
                'content' => $faker->paragraph(10),
                'notes' => $faker->paragraph(5),
                'unit_id' => $unit_id,
                'owner_id' => $owner_id,
                'photos' => $faker->imageUrl(800, 600) . ' , ' . $faker->imageUrl(800, 600) . ' , ' . $faker->imageUrl(800, 600),
                'condominio_id' => $condominio_id,
            ];
        }
        DB::table('warnings')->insert($warning);


        for ($i = 1; $i <= 10; $i++) {
            DB::table('lost_end_found')->insert([
                'title' => $faker->sentence(5),
                'content' => $faker->paragraph(10),
                'where' => $faker->sentence(5),
                'notes' => $faker->paragraph(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    private function createFolder($parent_id, $faker)
    {
        $folder = new \App\Models\Folder([
            'title' => $faker->word,
            'content' => $faker->paragraph,
        ]);

        $folder->parent_id = $parent_id;
        $folder->save();

        // Seed subpastas
        if (rand(0, 1) == 1) {
            $numSubfolders = rand(1, 5);
            for ($i = 0; $i < $numSubfolders; $i++) {
                $this->createFolder($folder->id, $faker);
            }
        }
    }
}
