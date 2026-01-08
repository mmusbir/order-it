<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $password = Hash::make('password'); // Default password

        User::firstOrCreate(
            ['email' => 'admin@order-it.local'],
            ['name' => 'IT Admin', 'role' => 'admin', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'requester@order-it.local'],
            ['name' => 'Regional IT Staff', 'role' => 'requester', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'manager@order-it.local'],
            ['name' => 'IT Manager', 'role' => 'manager', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'head@order-it.local'],
            ['name' => 'IT Head', 'role' => 'head', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'director@order-it.local'],
            ['name' => 'IT Director', 'role' => 'director', 'password' => $password]
        );

        // Products
        Product::firstOrCreate(
            ['name' => 'Lenovo ThinkPad X1 Carbon Gen 10'],
            [
                'specs' => 'Intel Core i7-1260P, 16GB RAM, 512GB SSD, 14" WUXGA',
                'price' => 28500000,
                'snipeit_model_id' => 101,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Dell Latitude 7420'],
            [
                'specs' => 'Intel Core i5-1145G7, 16GB RAM, 256GB SSD, 14" FHD',
                'price' => 18900000,
                'snipeit_model_id' => 102,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'MacBook Pro 14 M2 Pro'],
            [
                'specs' => 'Apple M2 Pro Chip, 16GB Memory, 512GB SSD',
                'price' => 31999000,
                'snipeit_model_id' => 103,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Logitech MX Master 3S'],
            [
                'specs' => 'Wireless Mouse, 8000 DPI, Silent Clicks',
                'price' => 1650000,
                'snipeit_model_id' => 201,
            ]
        );
    }
}
