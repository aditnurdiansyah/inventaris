@php
    $item = $item ?? null;
    $isEdit = $item && $item->exists;
@endphp

<form method="POST" action="{{ $isEdit ? route('items.update', $item) : route('items.store') }}" class="card form-card" novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <x-field name="code" label="Kode Barang" :value="$item?->code" placeholder="Contoh: BRG-016" maxlength="30" />
    <x-field name="name" label="Nama Barang" :value="$item?->name" maxlength="150" />

    <div class="field-row">
        <div>
            <x-field name="category" label="Kategori" :value="$item?->category" list="category-list" maxlength="80" />
        </div>
        <div>
            <x-field name="unit" label="Satuan" :value="$item?->unit" placeholder="pcs, kg, box" maxlength="20" />
        </div>
    </div>
    <datalist id="category-list">
        @foreach ($categories as $category)
            <option value="{{ $category }}"></option>
        @endforeach
    </datalist>

    <div class="field-row">
        <div>
            <x-field name="buy_price" label="Harga Beli (Rp)" type="number" :value="$item?->buy_price ?? 0" min="0" step="any" inputmode="decimal" />
        </div>
        <div>
            <x-field name="sell_price" label="Harga Jual (Rp)" type="number" :value="$item?->sell_price ?? 0" min="0" step="any" inputmode="decimal" />
        </div>
    </div>

    <div class="field-row">
        <div>
            @if ($isEdit)
                <div class="field">
                    <span class="label">Stok Saat Ini</span>
                    <div class="input input-readonly">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</div>
                    <p class="hint">Stok hanya berubah lewat Barang Masuk, Barang Keluar, atau Stok Opname.</p>
                </div>
            @else
                <x-field name="stock" label="Stok Awal" type="number" :value="0" min="0" step="1" hint="Hanya diisi saat barang dibuat." />
            @endif
        </div>
        <div>
            <x-field name="min_stock" label="Stok Minimum" type="number" :value="$item?->min_stock ?? 5" min="0" step="1" hint="Alert muncul bila stok sama dengan atau di bawah angka ini." />
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
