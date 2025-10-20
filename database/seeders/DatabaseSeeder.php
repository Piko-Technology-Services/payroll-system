<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB; // <-- Add this line
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Delete existing test user
        DB::table('users')->where('email', 'test@example.com')->delete();

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed other data
        $this->call([
            DefaultEarningsAndDeductionsSeeder::class,
            EmployeesTableSeeder::class,
        ]);
    }
}
