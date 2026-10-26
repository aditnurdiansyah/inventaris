@extends('layouts.app')

@section('title', 'Stok Menipis')
@section('subtitle', 'Barang dengan stok sama dengan atau di bawah batas minimum.')

@section('content')
    <form method="GET" action="{{ route('low-stock') }}" class="filters">
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
        @if (request()->filled('category'))
            <a href="{{ route('low-stock') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="num">Stok</th>
                    <th class="num">Minimum</th>
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
                        <td class="num">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</td>
                        <td class="num">{{ number_format($item->min_stock, 0, ',', '.') }}</td>
                        <td><x-status-badge :item="$item" /></td>
                        <td class="actions">
                            <a href="{{ route('stock-ins.create', ['item_id' => $item->id]) }}" class="link">Catat barang masuk</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Semua stok aman. Tidak ada barang yang perlu dipesan ulang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links('partials.pagination') }}
@endsection
