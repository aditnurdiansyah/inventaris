<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOut;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $stockOuts = StockOut::with(['item', 'user'])
            ->when($request->date_from, fn ($q, $d) => $q->where('date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('date', '<=', $d))
            ->when($request->item_id, fn ($q, $id) => $q->where('item_id', $id))
            ->when($request->reason, fn ($q, $r) => $q->where('reason', $r))
            ->latest('date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-outs.index', [
            'stockOuts' => $stockOuts,
            'items'     => Item::orderBy('name')->get(['id', 'code', 'name']),
            'reasons'   => StockOut::REASONS,
        ]);
    }

    public function create()
    {
        return view('stock-outs.create', [
            'items'   => Item::orderBy('name')->get(),
            'reasons' => StockOut::REASONS,
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'date'     => ['required', 'date', 'before_or_equal:today'],
            'item_id'  => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason'   => ['required', Rule::in(StockOut::REASONS)],
            'note'     => ['nullable', 'string', 'max:255'],
        ]);

        // Melempar ValidationException bila stok tidak cukup.
        $stockOut = $stock->recordOut($data, $request->user()->id);
        $item     = Item::find($stockOut->item_id);

        return redirect()->route('stock-outs.index')->with(
            'success',
            "Barang keluar berhasil disimpan. Stok {$item->name} kini {$item->stock} {$item->unit}."
        );
    }

    public function destroy(Request $request, StockOut $stockOut, StockService $stock)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $stock->cancelOut($stockOut);

        return back()->with('success', 'Transaksi barang keluar dibatalkan dan stok dikembalikan.');
    }
}
