<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
    $lowCount = \App\Models\Item::lowStock()->count();
    $menu = [
        ['route' => 'dashboard',        'match' => 'dashboard',        'label' => 'Dashboard',       'icon' => 'dashboard'],
        ['group' => 'Data Master'],
        ['route' => 'items.index',      'match' => 'items.*',          'label' => 'Barang',          'icon' => 'package'],
        ['route' => 'suppliers.index',  'match' => 'suppliers.*',      'label' => 'Pemasok',         'icon' => 'truck'],
        ['group' => 'Transaksi'],
        ['route' => 'stock-ins.index',  'match' => 'stock-ins.*',      'label' => 'Barang Masuk',    'icon' => 'arrow-in'],
        ['route' => 'stock-outs.index', 'match' => 'stock-outs.*',     'label' => 'Barang Keluar',   'icon' => 'arrow-out'],
        ['route' => 'stock-opnames.index', 'match' => 'stock-opnames.*', 'label' => 'Stok Opname',   'icon' => 'clipboard'],
        ['group' => 'Pantauan'],
        ['route' => 'low-stock',        'match' => 'low-stock',        'label' => 'Stok Menipis',    'icon' => 'alert'],
        ['group' => 'Laporan'],
        ['route' => 'reports.daily',    'match' => 'reports.daily',    'label' => 'Laporan Harian',  'icon' => 'file'],
        ['route' => 'reports.monthly',  'match' => 'reports.monthly',  'label' => 'Laporan Bulanan', 'icon' => 'calendar'],
    ];
@endphp

<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            {{ config('app.name') }}
            <small>Gudang dan toko</small>
        </div>

        <nav class="nav" aria-label="Menu utama">
            @foreach ($menu as $m)
                @if (isset($m['group']))
                    <p class="nav-label">{{ $m['group'] }}</p>
                @else
                    <a href="{{ route($m['route']) }}" class="nav-link {{ request()->routeIs($m['match']) ? 'active' : '' }}">
                        <x-icon :name="$m['icon']" />
                        <span>{{ $m['label'] }}</span>
                        @if ($m['route'] === 'low-stock' && $lowCount > 0)
                            <span class="nav-badge">{{ $lowCount }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="sidebar-user">
            <p class="name">{{ auth()->user()->name }}</p>
            <p class="muted small">{{ auth()->user()->isAdmin() ? 'Admin' : 'Staf Gudang' }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width:100%">
                    <x-icon name="log-out" /> Keluar
                </button>
            </form>
        </div>
    </aside>
    <div class="overlay" id="overlay"></div>

    <main class="main">
        <div class="topbar">
            <button type="button" class="btn btn-secondary" id="menu-toggle" aria-label="Buka menu" style="padding:0 10px">
                <x-icon name="menu" />
            </button>
            <span class="brand-inline">{{ config('app.name') }}</span>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="alert alert-success" role="status" data-autohide>{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            <header class="page-header">
                <div>
                    <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="page-subtitle">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="page-actions">@yield('actions')</div>
            </header>

            @yield('content')
        </div>
    </main>
</div>

<dialog id="confirm-dialog" class="modal">
    <h2 class="modal-title" data-confirm-title>Konfirmasi</h2>
    <p class="modal-text" data-confirm-text></p>
    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" data-confirm-cancel>Batal</button>
        <button type="button" class="btn btn-primary" data-confirm-ok>Lanjutkan</button>
    </div>
</dialog>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
