<?php

namespace Database\Seeders;

use App\Models\Drink;
use Illuminate\Database\Seeder;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinks = [
            [
                'name' => 'Green Goddess',
                'price' => 8.99,
                'category' => 'Smoothies',
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=400&h=300&fit=crop&crop=center',
                'description' => 'A refreshing blend of spinach, kale, and apple.',
            ],
            [
                'name' => 'Tropical Paradise',
                'price' => 9.49,
                'category' => 'Smoothies',
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=400&h=300&fit=crop&crop=center',
                'description' => 'Mango, pineapple, and coconut milk for a tropical escape.',
            ],
            [
                'name' => 'Fresh Orange Juice',
                'price' => 5.99,
                'category' => 'Juices',
                'is_featured' => false,
                'image_url' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=300&fit=crop&crop=center',
                'description' => 'Freshly squeezed oranges, no added sugar.',
            ],
            [
                'name' => 'Mango Lassi',
                'price' => 4.99,
                'category' => 'Seasonal',
                'is_featured' => true,
                'image_url' => 'https://picsum.photos/seed/ss4/400/300',
                'description' => 'Traditional yogurt-based drink with ripe mango pulp.',
            ],
            [
                'name' => 'Berry Blast',
                'price' => 5.49,
                'category' => 'Smoothies',
                'is_featured' => false,
                'image_url' => 'https://picsum.photos/seed/sm2/400/300',
                'description' => 'Mixed berries with a hint of honey.',
            ],
            [
                'name' => 'Pumpkin Spice Smoothie',
                'price' => 6.49,
                'category' => 'Seasonal',
                'is_featured' => true,
                'image_url' => 'https://picsum.photos/seed/ss1/400/300',
                'description' => 'Seasonal favorite with pumpkin puree and spices.',
            ],
        ];

        foreach ($drinks as $drink) {
            Drink::create($drink);
        }
    }
}
