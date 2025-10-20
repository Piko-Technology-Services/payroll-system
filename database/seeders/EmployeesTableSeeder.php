<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class EmployeesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Example Zambian companies, branches, departments, and positions
        $companies = ['Best Choice Trading and Manufacturing Ltd', 'ZamSteel Ltd', 'Chibuku Manufacturing'];
        $branches = ['Lusaka', 'Ndola', 'Kitwe', 'Chingola'];
        $departments = ['Production', 'Quality Control', 'HR', 'Finance', 'Maintenance', 'Logistics'];
        $positions = ['Manager', 'Supervisor', 'Technician', 'Operator', 'Accountant', 'HR Officer'];
        $pay_methods = ['Bank', 'Cash'];

        // Common Zambian banks
        $banks = [
            'Zanaco', 'Stanbic', 'Standard Chartered', 'Ecobank', 'Independence Bank', 'Atlas Mara'
        ];

        // Sample realistic Zambian-style names
        $firstNames = ['Chileshe','Mwamba','Lackson','Thandiwe','Mutinta','Chansa','Bwalya','Lusungu','Sipho','Chibale'];
        $lastNames = ['Nkandu','Phiri','Mwila','Chirwa','Kalumba','Mbewe','Tembo','Kunda','Kapembwa','Zimba'];

        for ($i = 0; $i < 50; $i++) {
            $fullnames = $faker->randomElement($firstNames) . ' ' . $faker->randomElement($lastNames);

            DB::table('employees')->insert([
                'fullnames' => $fullnames,
                'employee_id' => strtoupper(Str::random(6)),
                'date_engaged' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'salary_rate' => $faker->numberBetween(4000, 25000), // realistic monthly ZMW salaries
                'company' => $faker->randomElement($companies),
                'branch' => $faker->randomElement($branches),
                'department' => $faker->randomElement($departments),
                'position' => $faker->randomElement($positions),
                'pay_method' => $faker->randomElement($pay_methods),
                'bank_acc_number' => $faker->numerify('010#########'),
                'nrc_number' => $faker->numerify('########/[A-Z]'), // example: 12345678/L
                'ssn' => $faker->numerify('###-##-####'),
                'nhi_no' => 'NHI-' . $faker->numerify('#####'),
                'leave_days' => $faker->numberBetween(14, 30),
                'tpin' => 'TPIN' . $faker->numerify('########'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
