@extends('layouts.app')

@section('title', 'Catat Barang Keluar')

@section('content')
    <a href="{{ route('stock-outs.index') }}" class="back-link">Kembali ke riwayat barang keluar</a>

    <form method="POST" action="{{ route('stock-outs.store') }}" class="card form-card" novalidate>
        @csrf

        <x-field name="date" label="Tanggal" type="date" :value="today()->toDateString()" :max="today()->toDateString()" />

        <x-select name="item_id" label="Barang" data-item-select>
            <option value="">Pilih barang</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}"
                        data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}"
                        @selected(old('item_id', request('item_id')) == $item->id)>
                    {{ $item->code }} - {{ $item->name }}
                </option>
            @endforeach
        </x-select>

        <div class="field-row">
            <div>
                <x-field name="quantity" label="Jumlah" type="number" min="1" step="1" inputmode="numeric" />
            </div>
            <div>
                <x-select name="reason" label="Alasan">
                    @foreach ($reasons as $reason)
                        <option value="{{ $reason }}" @selected(old('reason', 'terjual') === $reason)>{{ ucfirst($reason) }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <x-field name="note" label="Catatan" :required="false" maxlength="255" />

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
