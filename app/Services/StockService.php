<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOpname;
use App\Models\StockOut;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Seluruh perubahan stok lewat class ini.
 * Setiap method dibungkus DB::transaction dan mengunci baris barang
 * (lockForUpdate) agar stok tidak selisih saat dua pengguna mengakses bersamaan.
 */
class StockService
{
    public function recordIn(array $data, int $userId): StockIn
    {
        return DB::transaction(function () use ($data, $userId) {
            $item = $this->lockItem($data['item_id']);

            $stockIn = StockIn::create([
                'item_id'     => $item->id,
                'supplier_id' => $data['supplier_id'],
                'user_id'     => $userId,
                'date'        => $data['date'],
                'quantity'    => $data['quantity'],
                'buy_price'   => $data['buy_price'],
                'note'        => $data['note'] ?? null,
            ]);

            $item->increment('stock', $data['quantity']);

            return $stockIn;
        });
    }

    public function cancelIn(StockIn $stockIn): void
    {
        DB::transaction(function () use ($stockIn) {
            $item = $this->lockItem($stockIn->item_id);

            if ($item->stock < $stockIn->quantity) {
                throw new DomainException(
                    "Transaksi tidak dapat dibatalkan karena stok {$item->name} saat ini ({$item->stock}) "
                    . "lebih kecil dari jumlah yang akan ditarik ({$stockIn->quantity})."
                );
            }

            $item->decrement('stock', $stockIn->quantity);
            $stockIn->delete();
        });
    }

    public function recordOut(array $data, int $userId): StockOut
    {
        return DB::transaction(function () use ($data, $userId) {
            $item = $this->lockItem($data['item_id']);

            if ($item->stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Jumlah melebihi stok tersedia ({$item->stock} {$item->unit}). Kurangi jumlah barang keluar.",
                ]);
            }

            $stockOut = StockOut::create([
                'item_id'  => $item->id,
                'user_id'  => $userId,
                'date'     => $data['date'],
                'quantity' => $data['quantity'],
                'reason'   => $data['reason'],
                'note'     => $data['note'] ?? null,
            ]);

            $item->decrement('stock', $data['quantity']);

            return $stockOut;
        });
    }

    public function cancelOut(StockOut $stockOut): void
    {
        DB::transaction(function () use ($stockOut) {
            $item = $this->lockItem($stockOut->item_id);
            $item->increment('stock', $stockOut->quantity);
            $stockOut->delete();
        });
    }

    /**
     * @param array<int,int> $physical  [item_id => stok fisik]
     * @return int jumlah barang yang diopname
     */
    public function recordOpname(array $physical, string $date, ?string $note, int $userId): int
    {
        return DB::transaction(function () use ($physical, $date, $note, $userId) {
            foreach ($physical as $itemId => $physicalStock) {
                $item = $this->lockItem((int) $itemId);

                StockOpname::create([
                    'item_id'        => $item->id,
                    'user_id'        => $userId,
                    'date'           => $date,
                    'system_stock'   => $item->stock,
                    'physical_stock' => $physicalStock,
                    'difference'     => $physicalStock - $item->stock,
                    'note'           => $note,
                ]);

                $item->update(['stock' => $physicalStock]);
            }

            return count($physical);
        });
    }

    private function lockItem(int $id): Item
    {
        return Item::whereKey($id)->lockForUpdate()->firstOrFail();
    }
}
