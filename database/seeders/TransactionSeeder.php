<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // hasil seeder sama di setiap komputer anggota tim

        $userIds     = User::pluck('id')->all();
        $supplierIds = Supplier::pluck('id')->all();
        $reasons     = ['terjual', 'terjual', 'terjual', 'terjual', 'rusak', 'kedaluwarsa'];

        DB::transaction(function () use ($userIds, $supplierIds, $reasons) {
            foreach (Item::orderBy('id')->get() as $index => $item) {
                $stock = 0;

                // 2 transaksi masuk, 15-30 hari lalu
                for ($i = 0; $i < 2; $i++) {
                    $qty = mt_rand(30, 120);
                    StockIn::create([
                        'item_id'     => $item->id,
                        'supplier_id' => $supplierIds[array_rand($supplierIds)],
                        'user_id'     => $userIds[array_rand($userIds)],
                        'date'        => now()->subDays(mt_rand(15, 30))->toDateString(),
                        'quantity'    => $qty,
                        'buy_price'   => $item->buy_price,
                        'note'        => 'Pengadaan rutin',
                    ]);
                    $stock += $qty;
                }

                // 3 transaksi keluar, 0-14 hari lalu (selalu setelah barang masuk)
                for ($i = 0; $i < 3; $i++) {
                    $qty = mt_rand(5, max(5, (int) ($stock * 0.3)));
                    if ($qty > $stock) {
                        continue;
                    }
                    $this->makeOut($item, $qty, $reasons[array_rand($reasons)], $userIds);
                    $stock -= $qty;
                }

                // Skenario demo alert: barang pertama menipis, barang kedua habis
                if ($index === 0) {
                    $target = max(0, $item->min_stock - 2);
                } elseif ($index === 1) {
                    $target = 0;
                } else {
                    $target = null;
                }

                if ($target !== null && $stock > $target) {
                    $this->makeOut($item, $stock - $target, 'terjual', $userIds);
                    $stock = $target;
                }

                $item->update(['stock' => $stock]);
            }
        });
    }

    private function makeOut(Item $item, int $qty, string $reason, array $userIds): void
    {
        StockOut::create([
            'item_id'  => $item->id,
            'user_id'  => $userIds[array_rand($userIds)],
            'date'     => now()->subDays(mt_rand(0, 14))->toDateString(),
            'quantity' => $qty,
            'reason'   => $reason,
            'note'     => null,
        ]);
    }
}
