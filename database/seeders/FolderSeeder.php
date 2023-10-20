<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;



class FolderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Seed 5 pastas raiz
        for ($i = 0; $i < 5; $i++) {
            $this->createFolder(null, $faker);
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
