<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdrianoUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Adriano Alves';
        $user->email = 'adrianobr00@gmail.com';
        $user->cpf = '12345678901'; // adicionado um valor aleatório para a coluna cpf
        $user->profile = 1;
        $user->password = Hash::make('0307199216@Dr');
        $user->save();

        $user = new User();
        $user->name = 'Administrador';
        $user->email = 'admin@admin.com';
        $user->cpf = '12345678998'; // adicionado um valor aleatório para a coluna cpf
        $user->profile = 1;
        $user->password = Hash::make('12345');
        $user->save();
    }
}
