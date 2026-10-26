<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category', 'unit',
        'buy_price', 'sell_price', 'stock', 'min_stock',
    ];

    protected $casts = [
        'buy_price'  => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock'      => 'integer',
        'min_stock'  => 'integer',
    ];

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class);
    }

    /** Barang menipis: stok <= batas minimum (termasuk yang habis). */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /** Status untuk badge: aman, menipis, habis. */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }

        return $this->stock <= $this->min_stock ? 'menipis' : 'aman';
    }

    /** Barang yang sudah punya transaksi tidak boleh dihapus. */
    public function hasTransactions(): bool
    {
        return $this->stockIns()->exists()
            || $this->stockOuts()->exists()
            || $this->stockOpnames()->exists();
    }
}
