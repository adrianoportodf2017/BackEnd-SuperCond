<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\Condominios;

class CondominiosTableSeeder extends Seeder
{
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
    }
}
