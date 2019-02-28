<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

//        our tables have foreign keys btw them so we need to temporarily disable the
//        foreign key checks for the database seeder
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        \App\User::truncate();
        \App\Category::truncate();
        \App\Product::truncate();
        \App\Transaction::truncate();

        \Illuminate\Support\Facades\DB::table('category_product')->truncate();

        $usersQuantity = 1000;
        $categoriesQuantity = 30;
        $productsQuantity = 1000;
        $transactionsQuantity = 1000;

        factory(\App\User::class, $usersQuantity)->create();
        factory(\App\Category::class, $categoriesQuantity)->create();

//      for every product that we create, we need to associate a category with it, and for this
//      we are going to create a function that generates a random category id and attach to a product
        factory(\App\Product::class, $productsQuantity)->create()->each(
            function ($product){
                $categories = \App\Category::all()->random(mt_rand(1, 5))->pluck('id');
                $product->categories()->attach($categories);
            }
        );

        factory(\App\Transaction::class, $transactionsQuantity)->create();

    }
}
