<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Stok awal 0; stok diisi oleh TransactionSeeder lewat transaksi masuk/keluar
        // supaya stok selalu cocok dengan riwayat.
        $items = [
            // code, name, category, unit, buy_price, sell_price, min_stock
            ['BRG-001', 'Beras Premium 5 kg',      'Sembako',     'karung', 62000,  68000,  15],
            ['BRG-002', 'Minyak Goreng 2 L',       'Sembako',     'pouch',  32000,  36000,  20],
            ['BRG-003', 'Gula Pasir 1 kg',         'Sembako',     'kg',     14500,  16500,  20],
            ['BRG-004', 'Tepung Terigu 1 kg',      'Sembako',     'kg',     10500,  12500,  15],
            ['BRG-005', 'Mie Instan Goreng',       'Makanan',     'pcs',     2800,   3500,  50],
            ['BRG-006', 'Biskuit Kelapa 300 g',    'Makanan',     'pcs',     9000,  11500,  15],
            ['BRG-007', 'Air Mineral 600 ml',      'Minuman',     'botol',   2200,   3000,  48],
            ['BRG-008', 'Teh Celup 25 pcs',        'Minuman',     'box',     6500,   8500,  12],
            ['BRG-009', 'Kopi Bubuk 200 g',        'Minuman',     'pcs',    14000,  17500,  10],
            ['BRG-010', 'Sabun Cuci Piring 800 ml','Kebersihan',  'botol',  13500,  16500,  12],
            ['BRG-011', 'Deterjen Bubuk 1 kg',     'Kebersihan',  'pcs',    19000,  23500,  10],
            ['BRG-012', 'Tisu Wajah 250 sheet',    'Kebersihan',  'pcs',     8500,  11000,  12],
            ['BRG-013', 'Pulpen Hitam',            'ATK',         'pcs',     2500,   4000,  30],
            ['BRG-014', 'Buku Tulis 38 Lembar',    'ATK',         'pcs',     3800,   5500,  25],
            ['BRG-015', 'Kertas HVS A4 70 gsm',    'ATK',         'rim',    42000,  49000,   8],
        ];

        foreach ($items as [$code, $name, $category, $unit, $buy, $sell, $min]) {
            Item::create([
                'code'       => $code,
                'name'       => $name,
                'category'   => $category,
                'unit'       => $unit,
                'buy_price'  => $buy,
                'sell_price' => $sell,
                'stock'      => 0,
                'min_stock'  => $min,
            ]);
        }
    }
}
