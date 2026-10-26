<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOpname;
use App\Models\StockOut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Rumus laporan (dihitung mundur dari stok saat ini, sehingga tetap benar
 * walau barang dibuat dengan stok awal):
 *
 *   mutasi(periode) = masuk - keluar + penyesuaian opname
 *   stok akhir      = stok saat ini - mutasi(setelah periode)
 *   stok awal       = stok akhir - mutasi(periode)
 */
class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : today();

        return view('reports.show', [
            'mode'        => 'daily',
            'title'       => 'Laporan Stok Harian',
            'periodLabel' => $date->translatedFormat('d F Y'),
            'date'        => $date->toDateString(),
            'rows'        => $this->build($request, $date, $date),
            'categories'  => $this->categories(),
        ]);
    }

    public function monthly(Request $request)
    {
        $month = max(1, min(12, $request->integer('month', now()->month)));
        $year  = max(2000, min(2100, $request->integer('year', now()->year)));

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->startOfDay();

        return view('reports.show', [
            'mode'        => 'monthly',
            'title'       => 'Laporan Stok Bulanan',
            'periodLabel' => $start->translatedFormat('F Y'),
            'month'       => $month,
            'year'        => $year,
            'rows'        => $this->build($request, $start, $end),
            'categories'  => $this->categories(),
        ]);
    }

    private function build(Request $request, Carbon $start, Carbon $end): Collection
    {
        $zero   = ['in' => 0, 'out' => 0, 'adj' => 0];
        $period = $this->movements($start->toDateString(), $end->toDateString());
        $after  = $this->movements($end->copy()->addDay()->toDateString(), null);

        $rows = Item::query()
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->orderBy('name')
            ->get()
            ->map(function (Item $item) use ($period, $after, $zero) {
                $p = array_merge($zero, $period[$item->id] ?? []);
                $a = array_merge($zero, $after[$item->id] ?? []);

                $closing = $item->stock - ($a['in'] - $a['out'] + $a['adj']);
                $opening = $closing - ($p['in'] - $p['out'] + $p['adj']);

                return (object) [
                    'item'    => $item,
                    'opening' => $opening,
                    'in'      => $p['in'],
                    'out'     => $p['out'],
                    'adj'     => $p['adj'],
                    'closing' => $closing,
                ];
            });

        if ($request->boolean('active')) {
            $rows = $rows->filter(fn ($r) => $r->in || $r->out || $r->adj)->values();
        }

        return $rows;
    }

    /** @return array<int, array{in?:int,out?:int,adj?:int}> dikelompokkan per item_id */
    private function movements(string $from, ?string $to): array
    {
        $range = function ($query) use ($from, $to) {
            $query->where('date', '>=', $from);
            if ($to !== null) {
                $query->where('date', '<=', $to);
            }

            return $query;
        };

        $sources = [
            'in'  => $range(StockIn::query())->selectRaw('item_id, SUM(quantity) AS total')->groupBy('item_id')->pluck('total', 'item_id'),
            'out' => $range(StockOut::query())->selectRaw('item_id, SUM(quantity) AS total')->groupBy('item_id')->pluck('total', 'item_id'),
            'adj' => $range(StockOpname::query())->selectRaw('item_id, SUM(difference) AS total')->groupBy('item_id')->pluck('total', 'item_id'),
        ];

        $result = [];
        foreach ($sources as $key => $map) {
            foreach ($map as $itemId => $total) {
                $result[$itemId][$key] = (int) $total;
            }
        }

        return $result;
    }

    private function categories()
    {
        return Item::select('category')->distinct()->orderBy('category')->pluck('category');
    }
}
