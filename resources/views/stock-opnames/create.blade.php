@extends('layouts.app')

@section('title', 'Stok Opname')
@section('subtitle', 'Isi stok fisik hasil hitung. Barang yang dikosongkan tidak diproses.')

@section('content')
    <a href="{{ route('stock-opnames.index') }}" class="back-link">Kembali ke riwayat opname</a>

    @php
        $physicalError = collect($errors->keys())->first(fn ($key) => str_starts_with($key, 'physical'));
    @endphp
    @if ($physicalError)
        <div class="alert alert-danger" role="alert">{{ $errors->first($physicalError) }}</div>
    @endif

    <form method="POST" action="{{ route('stock-opnames.store') }}" novalidate
          data-confirm="Stok barang yang diisi akan diperbarui sesuai stok fisik dan selisihnya dicatat sebagai riwayat. Lanjutkan?"
          data-confirm-title="Konfirmasi opname" data-confirm-ok="Konfirmasi Opname">
        @csrf

        <div class="card form-card" style="margin-bottom: var(--space-6)">
            <x-field name="date" label="Tanggal Opname" type="date" :value="today()->toDateString()" :max="today()->toDateString()" />
            <x-field name="note" label="Catatan" :required="false" maxlength="255" placeholder="Contoh: Opname akhir bulan" />
        </div>

        <div class="filters">
            <label class="filter filter-search">
                <span>Cari barang</span>
                <x-icon name="search" />
                <input type="search" class="input" placeholder="Kode atau nama barang" data-table-search>
            </label>
        </div>

        <div class="table-wrap">
            <table class="table" data-searchable>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th class="num">Stok Sistem</th>
                        <th class="num">Stok Fisik</th>
                        <th class="num">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr data-row>
                            <td class="nowrap">{{ $item->code }}</td>
                            <td class="strong">{{ $item->name }}<span class="sub">{{ $item->category }}</span></td>
                            <td class="num">{{ number_format($item->stock, 0, ',', '.') }} {{ $item->unit }}</td>
                            <td class="num">
                                <input type="number" min="0" step="1" inputmode="numeric"
                                       name="physical[{{ $item->id }}]"
                                       value="{{ old('physical.' . $item->id) }}"
                                       class="input" style="width:120px;margin-left:auto"
                                       aria-label="Stok fisik {{ $item->name }}"
                                       data-physical data-system="{{ $item->stock }}">
                            </td>
                            <td class="num"><span data-diff class="muted">-</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Belum ada data barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Konfirmasi Opname</button>
            <a href="{{ route('stock-opnames.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
