<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dish;
use App\Models\Ingredient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ingredient_names = [
            'tomato',
            'lemon',
            'potato',
            'rice',
            'ketchup',
            'lettuce',
            'onion',
            'cheese',
            'meat',
            'chicken'
        ];

        foreach ($ingredient_names as $name) {
            Ingredient::create(['name' => $name]);
        }

        $dish_names = [
            'Lasagna',
            'Spaghetti Carbonara',
            'Chicken Alfredo',
            'Margherita Pizza',
            'Caesar Salad',
            'Beef Tacos'
        ];

        foreach ($dish_names as $dish_name) {
            $dish = Dish::create(['name' => $dish_name]);

            $ingredients = Ingredient::all()->random(rand(1, 5));
            $ingredients->each(function ($ingredient) use ($dish) {
                $dish->ingredients()->attach($ingredient, ['quantity' => rand(1, 10)]);
            });
        }
    }
}
