<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pizzaCategory = Category::query()->where('name', 'Пицца')->firstOrFail();
        $drinkCategory = Category::query()->where('name', 'Напитки')->firstOrFail();

        Product::query()->updateOrCreate(
            [
            'category_id' => $pizzaCategory->id,
            'name' => 'Маргарита',
        ],
            [
            'description' => 'Тестовое описание пиццы 1',
            'price' => 320.00,
            'weight' => 400.00,
            'type' => 'pizza',
        ]
        );

        Product::query()->updateOrCreate(
            [
            'category_id' => $pizzaCategory->id,
            'name' => 'Четыре сыра',
        ],
            [
            'description' => 'Тестовое описание пиццы 2',
            'price' => 280.50,
            'weight' => 350.00,
            'type' => 'pizza',
        ]
        );

        Product::query()->updateOrCreate(
            [
            'category_id' => $pizzaCategory->id,
            'name' => 'Пепперони',
        ],
            [
            'description' => 'Тестовое описание пиццы 3',
            'price' => 419.99,
            'weight' => 450.00,
            'type' => 'pizza',
        ]
        );

        Product::query()->updateOrCreate(
            [
            'category_id' => $drinkCategory->id,
            'name' => 'Газировка',
        ],
            [
            'description' => 'Тестовое описание напитка 1',
            'price' => 99.99,
            'weight' => 1000.00,
            'type' => 'drink',
        ]
        );

        Product::query()->updateOrCreate(
            [
            'category_id' => $drinkCategory->id,
            'name' => 'Чай',
        ],
            [
            'description' => 'Тестовое описание напитка 2',
            'price' => 20.00,
            'weight' => 100.00,
            'type' => 'drink',
        ]
        );

        Product::query()->updateOrCreate(
            [
            'category_id' => $drinkCategory->id,
            'name' => 'Морс',
        ],
            [
            'description' => 'Тестовое описание напитка 3',
            'price' => 119.50,
            'weight' => 300.00,
            'type' => 'drink',
        ]
        );
    }
}
