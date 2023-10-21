<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Answer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Fetva-i-emin',
            'email' => 'admin@iz.ba',
        ]);

        User::factory()->admin()->create([
            'name' => 'Savjetnik',
            'email' => 'savjetnik@iz.ba',
        ]);

        Answer::factory()->count(100)->create();
    }
}
