<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Services\StockService;
use DomainException;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $stockIns = StockIn::with(['item', 'supplier', 'user'])
            ->when($request->date_from, fn ($q, $d) => $q->where('date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('date', '<=', $d))
            ->when($request->item_id, fn ($q, $id) => $q->where('item_id', $id))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->latest('date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-ins.index', [
            'stockIns'  => $stockIns,
            'items'     => Item::orderBy('name')->get(['id', 'code', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('stock-ins.create', [
            'items'     => Item::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'date'        => ['required', 'date', 'before_or_equal:today'],
            'item_id'     => ['required', 'exists:items,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity'    => ['required', 'integer', 'min:1'],
            'buy_price'   => ['required', 'numeric', 'min:0'],
            'note'        => ['nullable', 'string', 'max:255'],
        ]);

        $stockIn = $stock->recordIn($data, $request->user()->id);
        $item    = Item::find($stockIn->item_id);

        return redirect()->route('stock-ins.index')->with(
            'success',
            "Barang masuk berhasil disimpan. Stok {$item->name} kini {$item->stock} {$item->unit}."
        );
    }

    public function destroy(Request $request, StockIn $stockIn, StockService $stock)
    {
        abort_unless($request->user()->isAdmin(), 403);

        try {
            $stock->cancelIn($stockIn);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Transaksi barang masuk dibatalkan dan stok disesuaikan.');
    }
}
