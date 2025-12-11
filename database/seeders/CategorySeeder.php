<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Photos', 'slug' => 'photo', 'icon' => 'camera', 'sort_order' => 1],
            ['name' => 'Videos', 'slug' => 'video', 'icon' => 'video-camera', 'sort_order' => 2],
            ['name' => 'Audio', 'slug' => 'audio', 'icon' => 'musical-note', 'sort_order' => 3],
            ['name' => 'Templates', 'slug' => 'template', 'icon' => 'template', 'sort_order' => 4],
            ['name' => 'Graphics', 'slug' => 'graphic', 'icon' => 'photograph', 'sort_order' => 5],
            ['name' => '3D Models', 'slug' => '3d-models', 'icon' => 'cube', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
