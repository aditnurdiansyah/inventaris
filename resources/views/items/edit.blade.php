@extends('layouts.app')

@section('title', 'Ubah Barang')
@section('subtitle', $item->code)

@section('content')
    <a href="{{ route('items.index') }}" class="back-link">Kembali ke daftar barang</a>
    @include('items._form')
@endsection
