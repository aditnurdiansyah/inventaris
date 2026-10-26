<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    use HasFactory;

    public const REASONS = ['terjual', 'rusak', 'kedaluwarsa', 'lainnya'];

    protected $fillable = [
        'item_id', 'user_id', 'date', 'quantity', 'reason', 'note',
    ];

    protected $casts = [
        'date'     => 'date',
        'quantity' => 'integer',
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
