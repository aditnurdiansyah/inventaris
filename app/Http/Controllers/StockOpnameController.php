<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOpname;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $opnames = StockOpname::with(['item', 'user'])
            ->when($request->date_from, fn ($q, $d) => $q->where('date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('date', '<=', $d))
            ->when($request->only_diff, fn ($q) => $q->where('difference', '!=', 0))
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('stock-opnames.index', compact('opnames'));
    }

    public function create()
    {
        return view('stock-opnames.create', [
            'items' => Item::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'date'       => ['required', 'date', 'before_or_equal:today'],
            'note'       => ['nullable', 'string', 'max:255'],
            'physical'   => ['required', 'array'],
            'physical.*' => ['nullable', 'integer', 'min:0'],
        ]);

        // Hanya barang yang stok fisiknya diisi yang diproses.
        $rows = collect($data['physical'])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (empty($rows)) {
            throw ValidationException::withMessages([
                'physical' => 'Isi stok fisik minimal untuk satu barang.',
            ]);
        }

        if (Item::whereIn('id', array_keys($rows))->count() !== count($rows)) {
            throw ValidationException::withMessages([
                'physical' => 'Terdapat barang yang tidak valid.',
            ]);
        }

        $count = $stock->recordOpname($rows, $data['date'], $data['note'] ?? null, $request->user()->id);

        return redirect()->route('stock-opnames.index')
            ->with('success', "Stok opname selesai. Stok {$count} barang telah disesuaikan.");
    }
}
