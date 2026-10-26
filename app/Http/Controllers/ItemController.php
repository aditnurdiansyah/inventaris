<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->when($request->q, fn ($query, $term) => $query->where(
                fn ($w) => $w->where('code', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%")
            ))
            ->when($request->category, fn ($query, $c) => $query->where('category', $c))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('items.index', [
            'items'      => $items,
            'categories' => $this->categories(),
        ]);
    }

    public function create()
    {
        return view('items.create', ['categories' => $this->categories()]);
    }

    public function store(Request $request)
    {
        Item::create($request->validate($this->rules()));

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', [
            'item'       => $item,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $item->update($request->validate($this->rules($item)));

        return redirect()->route('items.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Request $request, Item $item)
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($item->hasTransactions()) {
            return back()->with('error', "{$item->name} sudah memiliki transaksi sehingga tidak dapat dihapus.");
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    private function rules(?Item $item = null): array
    {
        $rules = [
            'code'       => ['required', 'string', 'max:30', Rule::unique('items', 'code')->ignore($item)],
            'name'       => ['required', 'string', 'max:150'],
            'category'   => ['required', 'string', 'max:80'],
            'unit'       => ['required', 'string', 'max:20'],
            'buy_price'  => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'min_stock'  => ['required', 'integer', 'min:0'],
        ];

        // Stok awal hanya saat barang dibuat. Setelah itu stok berubah lewat transaksi.
        if (! $item) {
            $rules['stock'] = ['required', 'integer', 'min:0'];
        }

        return $rules;
    }

    private function categories()
    {
        return Item::select('category')->distinct()->orderBy('category')->pluck('category');
    }
}
