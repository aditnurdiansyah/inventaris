<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = today()->toDateString();

        $ins = StockIn::with('item')->latest('date')->latest('id')->limit(5)->get()
            ->map(fn ($t) => (object) [
                'type'     => 'Masuk',
                'date'     => $t->date,
                'item'     => $t->item->name,
                'unit'     => $t->item->unit,
                'quantity' => $t->quantity,
                'sort'     => [$t->date->timestamp, $t->id],
            ]);

        $outs = StockOut::with('item')->latest('date')->latest('id')->limit(5)->get()
            ->map(fn ($t) => (object) [
                'type'     => 'Keluar',
                'date'     => $t->date,
                'item'     => $t->item->name,
                'unit'     => $t->item->unit,
                'quantity' => $t->quantity,
                'sort'     => [$t->date->timestamp, $t->id],
            ]);

        return view('dashboard', [
            'totalItems'     => Item::count(),
            'totalSuppliers' => Supplier::count(),
            'inToday'        => (int) StockIn::where('date', $today)->sum('quantity'),
            'outToday'       => (int) StockOut::where('date', $today)->sum('quantity'),
            'lowCount'       => Item::lowStock()->count(),
            'lowItems'       => Item::lowStock()->orderBy('stock')->orderBy('name')->limit(5)->get(),
            'recent'         => $ins->concat($outs)->sortByDesc('sort')->take(5)->values(),
        ]);
    }
}
