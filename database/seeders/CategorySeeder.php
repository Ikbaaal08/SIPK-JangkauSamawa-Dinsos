<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bantuan Sosial',
                'code' => 'BANSOS',
                'description' => 'Aduan terkait bantuan sosial, penyaluran bantuan, dan data penerima manfaat.',
            ],
            [
                'name' => 'Penyandang Disabilitas',
                'code' => 'DIFABEL',
                'description' => 'Aduan terkait layanan, data, dan perlindungan penyandang disabilitas.',
            ],
            [
                'name' => 'Lansia',
                'code' => 'LANSIA',
                'description' => 'Aduan terkait layanan dan perlindungan lanjut usia.',
            ],
            [
                'name' => 'Anak dan Perempuan',
                'code' => 'ANPER',
                'description' => 'Aduan terkait perlindungan anak, perempuan, dan layanan pemberdayaan.',
            ],
            [
                'name' => 'Kemiskinan dan Ketahanan Sosial',
                'code' => 'KEMSOS',
                'description' => 'Aduan terkait kemiskinan, ketahanan sosial, dan pemberdayaan masyarakat.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
