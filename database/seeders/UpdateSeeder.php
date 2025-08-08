<?php

namespace Database\Seeders;

use App\Models\Update;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $staffUsers = User::where('role', 'staff')->pluck('id')->toArray();
        if (empty($staffUsers)) {
            $this->command->warn('No staff users found. No updates seeded.');
            return;
        }

        $faker = Factory::create();
        $updates = [];

        for ($i = 0; $i < 50; $i++) {
            $weeksAgo = 49 - $i; 
            $date = now()->subWeeks($weeksAgo + 1)->endOfWeek();
            $updates[] = [
                'user_id' => $staffUsers[array_rand($staffUsers)],
                'title' => $faker->sentence(6, true),
                'description' => $faker->paragraph(3, true),
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }
        Update::insert($updates);
    }
}
