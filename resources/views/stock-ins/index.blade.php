@extends('layouts.app')

@section('title', 'Barang Masuk')

@section('actions')
    <a href="{{ route('stock-ins.create') }}" class="btn btn-primary">Catat Barang Masuk</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('stock-ins.index') }}" class="filters">
        <label class="filter">
            <span>Dari tanggal</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
        </label>
        <label class="filter">
            <span>Sampai tanggal</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
        </label>
        <label class="filter">
            <span>Barang</span>
            <select name="item_id" class="input">
                <option value="">Semua barang</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" @selected(request('item_id') == $item->id)>{{ $item->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="filter">
            <span>Pemasok</span>
            <select name="supplier_id" class="input">
                <option value="">Semua pemasok</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        @if (request()->hasAny(['date_from', 'date_to', 'item_id', 'supplier_id']))
            <a href="{{ route('stock-ins.index') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Pemasok</th>
                    <th class="num">Jumlah</th>
                    <th class="num">Harga Beli</th>
                    <th>Dicatat Oleh</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockIns as $row)
                    <tr>
                        <td class="nowrap">{{ $row->date->translatedFormat('d M Y') }}</td>
                        <td class="strong">{{ $row->item->name }}<span class="sub">{{ $row->item->code }}</span></td>
                        <td>{{ $row->supplier->name }}</td>
                        <td class="num">{{ number_format($row->quantity, 0, ',', '.') }} {{ $row->item->unit }}</td>
                        <td class="num"><x-rp :value="$row->buy_price" /></td>
                        <td>{{ $row->user->name }}</td>
                        <td class="actions">
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('stock-ins.destroy', $row) }}" class="inline-form"
                                      data-confirm="Transaksi dibatalkan dan stok {{ $row->item->name }} dikurangi {{ $row->quantity }} {{ $row->item->unit }}."
                                      data-confirm-title="Batalkan barang masuk" data-confirm-ok="Batalkan transaksi" data-confirm-danger>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link link-danger">Batalkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada transaksi barang masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $stockIns->links('partials.pagination') }}
@endsection
