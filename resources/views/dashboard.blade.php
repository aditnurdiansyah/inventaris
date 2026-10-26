@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', today()->translatedFormat('l, d F Y'))

@section('content')
    <div class="stats">
        <div class="card">
            <p class="stat-label">Total Barang</p>
            <p class="stat-value">{{ number_format($totalItems, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <p class="stat-label">Total Pemasok</p>
            <p class="stat-value">{{ number_format($totalSuppliers, 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <p class="stat-label">Masuk Hari Ini</p>
            <p class="stat-value">{{ number_format($inToday, 0, ',', '.') }}</p>
            <p class="stat-note">unit barang</p>
        </div>
        <div class="card">
            <p class="stat-label">Keluar Hari Ini</p>
            <p class="stat-value">{{ number_format($outToday, 0, ',', '.') }}</p>
            <p class="stat-note">unit barang</p>
        </div>
    </div>

    @if ($lowCount > 0)
        <div class="alert alert-warning" role="status">
            Ada {{ $lowCount }} barang dengan stok menipis atau habis. Segera lakukan pemesanan ulang.
        </div>
    @endif

    <div class="two-col">
        <section>
            <div class="section-head">
                <h2 class="section-title">Stok Menipis</h2>
                <a href="{{ route('low-stock') }}" class="link">Lihat semua</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th class="num">Stok</th>
                            <th class="num">Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowItems as $item)
                            <tr>
                                <td>{{ $item->name }}<span class="sub">{{ $item->code }}</span></td>
                                <td class="num">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</td>
                                <td class="num">{{ number_format($item->min_stock, 0, ',', '.') }}</td>
                                <td><x-status-badge :item="$item" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Semua stok aman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <div class="section-head">
                <h2 class="section-title">Transaksi Terakhir</h2>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Barang</th>
                            <th class="num">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $t)
                            <tr>
                                <td class="nowrap">{{ $t->date->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $t->type === 'Masuk' ? 'badge-primary' : 'badge-neutral' }}">{{ $t->type }}</span>
                                </td>
                                <td>{{ $t->item }}</td>
                                <td class="num">{{ number_format($t->quantity, 0, ',', '.') }} {{ $t->unit }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
