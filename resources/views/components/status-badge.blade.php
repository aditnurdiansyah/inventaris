@props(['item'])
@php
    $map = [
        'aman'    => ['Aman', 'badge-success'],
        'menipis' => ['Menipis', 'badge-warning'],
        'habis'   => ['Habis', 'badge-danger'],
    ];
    [$label, $class] = $map[$item->stock_status];
@endphp
<span class="badge {{ $class }}">{{ $label }}</span>
