@extends('layouts.app')

@section('title', 'Barang')

@section('actions')
    <a href="{{ route('items.create') }}" class="btn btn-primary">Tambah Barang</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('items.index') }}" class="filters">
        <label class="filter filter-search">
            <span>Cari</span>
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Kode atau nama barang">
        </label>
        <label class="filter">
            <span>Kategori</span>
            <select name="category" class="input">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        @if (request()->hasAny(['q', 'category']))
            <a href="{{ route('items.index') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="num">Harga Beli</th>
                    <th class="num">Harga Jual</th>
                    <th class="num">Stok</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td class="nowrap">{{ $item->code }}</td>
                        <td class="strong">{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td class="num"><x-rp :value="$item->buy_price" /></td>
                        <td class="num"><x-rp :value="$item->sell_price" /></td>
                        <td class="num">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</td>
                        <td><x-status-badge :item="$item" /></td>
                        <td class="actions">
                            <a href="{{ route('items.edit', $item) }}" class="link">Ubah</a>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline-form"
                                      data-confirm="{{ $item->name }} akan dihapus permanen. Barang yang sudah memiliki transaksi tidak dapat dihapus."
                                      data-confirm-title="Hapus barang" data-confirm-ok="Hapus" data-confirm-danger>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link link-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links('partials.pagination') }}
@endsection
