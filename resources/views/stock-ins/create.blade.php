@extends('layouts.app')

@section('title', 'Catat Barang Masuk')

@section('content')
    <a href="{{ route('stock-ins.index') }}" class="back-link">Kembali ke riwayat barang masuk</a>

    <form method="POST" action="{{ route('stock-ins.store') }}" class="card form-card" novalidate>
        @csrf

        <x-field name="date" label="Tanggal" type="date" :value="today()->toDateString()" :max="today()->toDateString()" />

        <x-select name="item_id" label="Barang" data-item-select>
            <option value="">Pilih barang</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}"
                        data-buy-price="{{ $item->buy_price }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}"
                        @selected(old('item_id', request('item_id')) == $item->id)>
                    {{ $item->code }} - {{ $item->name }}
                </option>
            @endforeach
        </x-select>

        <x-select name="supplier_id" label="Pemasok">
            <option value="">Pilih pemasok</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </x-select>

        <div class="field-row">
            <div>
                <x-field name="quantity" label="Jumlah" type="number" min="1" step="1" inputmode="numeric" />
            </div>
            <div>
                <x-field name="buy_price" label="Harga Beli per Unit (Rp)" type="number" min="0" step="any" inputmode="decimal" />
            </div>
        </div>

        <x-field name="note" label="Catatan" :required="false" maxlength="255" placeholder="Nomor faktur atau keterangan lain" />

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('stock-ins.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
