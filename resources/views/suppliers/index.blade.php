@extends('layouts.app')

@section('title', 'Pemasok')

@section('actions')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Tambah Pemasok</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('suppliers.index') }}" class="filters">
        <label class="filter filter-search">
            <span>Cari</span>
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" class="input" placeholder="Nama, telepon, atau email">
        </label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        @if (request()->filled('q'))
            <a href="{{ route('suppliers.index') }}" class="btn btn-text">Atur Ulang</a>
        @endif
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="strong">{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone ?: '-' }}</td>
                        <td>{{ $supplier->email ?: '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($supplier->address, 50) ?: '-' }}</td>
                        <td class="actions">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="link">Ubah</a>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline-form"
                                      data-confirm="{{ $supplier->name }} akan dihapus permanen. Pemasok yang sudah dipakai pada transaksi tidak dapat dihapus."
                                      data-confirm-title="Hapus pemasok" data-confirm-ok="Hapus" data-confirm-danger>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link link-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada data pemasok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $suppliers->links('partials.pagination') }}
@endsection
