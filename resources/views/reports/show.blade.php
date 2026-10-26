@extends('layouts.app')

@section('title', $title)
@section('subtitle', $periodLabel)

@section('actions')
    <button type="button" class="btn btn-secondary" data-print>Cetak</button>
@endsection

@section('content')
    <form method="GET" action="{{ route($mode === 'daily' ? 'reports.daily' : 'reports.monthly') }}" class="filters">
        @if ($mode === 'daily')
            <label class="filter">
                <span>Tanggal</span>
                <input type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}" class="input">
            </label>
        @else
            <label class="filter">
                <span>Bulan</span>
                <select name="month" class="input">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($m === $month)>{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </label>
            <label class="filter">
                <span>Tahun</span>
                <select name="year" class="input">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                    @endfor
                </select>
            </label>
        @endif

        <label class="filter">
            <span>Kategori</span>
            <select name="category" class="input">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </label>

        <label class="checkbox filter-check">
            <input type="checkbox" name="active" value="1" @checked(request()->boolean('active'))>
            <span>Hanya barang yang bergerak</span>
        </label>

        <button type="submit" class="btn btn-secondary">Terapkan</button>
    </form>

    <p class="print-only muted small" style="margin-bottom: var(--space-4)">Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="num">Stok Awal</th>
                    <th class="num">Masuk</th>
                    <th class="num">Keluar</th>
                    <th class="num">Penyesuaian Opname</th>
                    <th class="num">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td class="nowrap">{{ $r->item->code }}</td>
                        <td class="strong">{{ $r->item->name }}</td>
                        <td>{{ $r->item->category }}</td>
                        <td>{{ $r->item->unit }}</td>
                        <td class="num">{{ number_format($r->opening, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($r->in, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($r->out, 0, ',', '.') }}</td>
                        <td class="num {{ $r->adj < 0 ? 'diff-neg' : ($r->adj > 0 ? 'diff-pos' : '') }}">{{ $r->adj > 0 ? '+' : '' }}{{ number_format($r->adj, 0, ',', '.') }}</td>
                        <td class="num strong">{{ number_format($r->closing, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty">Tidak ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="muted small" style="margin-top: var(--space-4)">
        Stok akhir = stok awal + masuk - keluar + penyesuaian opname. Penyesuaian opname adalah selisih antara stok fisik dan stok sistem pada saat opname.
    </p>
@endsection
