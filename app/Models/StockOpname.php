<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'user_id', 'date',
        'system_stock', 'physical_stock', 'difference', 'note',
    ];

    protected $casts = [
        'date'           => 'date',
        'system_stock'   => 'integer',
        'physical_stock' => 'integer',
        'difference'     => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
