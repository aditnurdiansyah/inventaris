@extends('layouts.app')

@section('title', 'Barang Keluar')

@section('actions')
    <a href="{{ route('stock-outs.create') }}" class="btn btn-primary">Catat Barang Keluar</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('stock-outs.index') }}" class="filters">
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
            <span>Alasan</span>
            <select name="reason" class="input">
                <option value="">Semua alasan</option>
                @foreach ($reasons as $reason)
                    <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ ucfirst($reason) }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        @if (request()->hasAny(['date_from', 'date_to', 'item_id', 'reason']))
            <a href="{{ route('stock-outs.index') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th class="num">Jumlah</th>
                    <th>Alasan</th>
                    <th>Catatan</th>
                    <th>Dicatat Oleh</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockOuts as $row)
                    <tr>
                        <td class="nowrap">{{ $row->date->translatedFormat('d M Y') }}</td>
                        <td class="strong">{{ $row->item->name }}<span class="sub">{{ $row->item->code }}</span></td>
                        <td class="num">{{ number_format($row->quantity, 0, ',', '.') }} {{ $row->item->unit }}</td>
                        <td>
                            <span class="badge {{ $row->reason === 'terjual' ? 'badge-primary' : 'badge-neutral' }}">{{ ucfirst($row->reason) }}</span>
                        </td>
                        <td>{{ $row->note ?: '-' }}</td>
                        <td>{{ $row->user->name }}</td>
                        <td class="actions">
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('stock-outs.destroy', $row) }}" class="inline-form"
                                      data-confirm="Transaksi dibatalkan dan stok {{ $row->item->name }} bertambah {{ $row->quantity }} {{ $row->item->unit }}."
                                      data-confirm-title="Batalkan barang keluar" data-confirm-ok="Batalkan transaksi" data-confirm-danger>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link link-danger">Batalkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada transaksi barang keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $stockOuts->links('partials.pagination') }}
@endsection
