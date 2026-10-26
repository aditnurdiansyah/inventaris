@extends('layouts.app')

@section('title', 'Stok Opname')

@section('actions')
    <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">Mulai Opname</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('stock-opnames.index') }}" class="filters">
        <label class="filter">
            <span>Dari tanggal</span>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
        </label>
        <label class="filter">
            <span>Sampai tanggal</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
        </label>
        <label class="checkbox filter-check">
            <input type="checkbox" name="only_diff" value="1" @checked(request()->boolean('only_diff'))>
            <span>Hanya yang berselisih</span>
        </label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        @if (request()->hasAny(['date_from', 'date_to', 'only_diff']))
            <a href="{{ route('stock-opnames.index') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th class="num">Stok Sistem</th>
                    <th class="num">Stok Fisik</th>
                    <th class="num">Selisih</th>
                    <th>Catatan</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opnames as $row)
                    <tr>
                        <td class="nowrap">{{ $row->date->translatedFormat('d M Y') }}</td>
                        <td class="strong">{{ $row->item->name }}<span class="sub">{{ $row->item->code }}</span></td>
                        <td class="num">{{ number_format($row->system_stock, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($row->physical_stock, 0, ',', '.') }}</td>
                        <td class="num {{ $row->difference < 0 ? 'diff-neg' : ($row->difference > 0 ? 'diff-pos' : '') }}">
                            {{ $row->difference > 0 ? '+' : '' }}{{ number_format($row->difference, 0, ',', '.') }}
                        </td>
                        <td>{{ $row->note ?: '-' }}</td>
                        <td>{{ $row->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada riwayat stok opname.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $opnames->links('partials.pagination') }}
@endsection
