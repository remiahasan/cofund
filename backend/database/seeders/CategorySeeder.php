<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories=[
            'Teknologi',
            'Pendidikan',
            'Kesehatan',
            'Kuliner',
            'Sosial',
            'Lingkungan'
        ];

        foreach($categories as $item){
            Category::create([
                'name'=>$item,
                'slug'=>str()->slug($item)
            ]);
        }
    }
}