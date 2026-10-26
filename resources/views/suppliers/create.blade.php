@extends('layouts.app')

@section('title', 'Tambah Pemasok')

@section('content')
    <a href="{{ route('suppliers.index') }}" class="back-link">Kembali ke daftar pemasok</a>
    @include('suppliers._form')
@endsection
