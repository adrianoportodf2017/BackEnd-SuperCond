<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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



        DB::table('condominios')->insert([
            'name' => 'Condomínio ABC',
            'codigo' => 'ABC123',
            'cnpj' => '11.111.111/0001-11',
            'adress' => 'Rua ABC, 123',
            'city' => 'São Paulo',
            'zip_code' => '12345-678',
            'adress_billit' => 'Rua ABC, 123',
            'description' => 'Condomínio de luxo',
            'thumb' => 'condominio-abc.jpg',
        ]);
        /* INSERINDO UNIDADES   */

        DB::table('units')->insert([
            'name' => 'APT 100',
            'id_owner' => 1,
            'id_condominio' => 1
        ]);
        DB::table('units')->insert([
            'name' => 'APT 101',
            'id_owner' => 1,
            'id_condominio' => 1

        ]);
        DB::table('units')->insert([
            'name' => 'APT 200',
            'id_owner' => '0',
            'id_condominio' => 1

        ]);
        DB::table('units')->insert([
            'name' => 'APT 201',
            'id_owner' => '0',
            'id_condominio' => 1

        ]);

        /*  INSERINDO AREAS */

        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Academia',
            'cover' => 'gym.jpg',
            'days' => '1,2,4,5',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'id_condominio' => 1

        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Piscina',
            'cover' => 'pool.jpg',
            'days' => '1,2,3,4,5',
            'start_time' => '07:00:00',
            'end_time' => '23:00:00',
            'id_condominio' => 1

        ]);
        DB::table('areas')->insert([
            'allowed' => '1',
            'title' => 'Churrasqueira',
            'cover' => 'barbecue.jpg',
            'days' => '4,5,6',
            'start_time' => '09:00:00',
            'end_time' => '22:00:00',
            'id_condominio' => 1

        ]);

        /*  INSERINDO AVISOS   */

        DB::table('walls')->insert([
            'title' => 'Título de Aviso de Teste',
            'body' => 'Lorem ipsum blablalba  lorem ipsim',
            'datecreated' => '2020-12-20 15:00:00',
            'id_condominio' => 1

        ]);
        DB::table('walls')->insert([
            'title' => 'Alerta geral para todos',
            'body' => 'Cuidado com blablalba  lorem ipsim',
            'datecreated' => '2020-12-20 18:00:00',
            'id_condominio' => 1

        ]);
    }
}