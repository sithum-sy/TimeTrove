<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service_category = [
            [
                'id' => 1,
                'name' => 'Electrical',
                'created_by' => 3,
                "is_active" => 1,
            ],
            [
                'id' => 2,
                'name' => 'Plumbing',
                'created_by' => 3,
                "is_active" => 1,
            ],
            [
                'id' => 3,
                'name' => 'Carpentry',
                'created_by' => 3,
                "is_active" => 1,
            ],
            [
                'id' => 4,
                'name' => 'Cleaning',
                'created_by' => 3,
                "is_active" => 1,
            ],
            [
                'id' => 5,
                'name' => 'Painting',
                'created_by' => 3,
                "is_active" => 1,
            ],



        ];

        DB::table('service_categories')->insert($service_category);
    }
}
