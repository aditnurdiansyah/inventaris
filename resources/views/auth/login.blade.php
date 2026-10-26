@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="login-card">
        <h1 class="page-title">{{ config('app.name') }}</h1>
        <p class="page-subtitle">Masuk untuk mengelola stok barang.</p>

        <form method="POST" action="{{ route('login.attempt') }}" novalidate>
            @csrf
            <x-field name="email" label="Email" type="email" autocomplete="email" autofocus />
            <x-field name="password" label="Password" type="password" autocomplete="current-password" />

            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>
    </div>
@endsection
