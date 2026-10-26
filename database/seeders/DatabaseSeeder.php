<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: transaksi butuh user, barang, dan pemasok lebih dulu.
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            ItemSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
