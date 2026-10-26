@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <a href="{{ route('items.index') }}" class="back-link">Kembali ke daftar barang</a>
    @include('items._form')
@endsection
