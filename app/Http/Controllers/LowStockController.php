<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class LowStockController extends Controller
{
    public function __invoke(Request $request)
    {
        $items = Item::lowStock()
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->orderBy('stock')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Item::lowStock()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('low-stock', compact('items', 'categories'));
    }
}
