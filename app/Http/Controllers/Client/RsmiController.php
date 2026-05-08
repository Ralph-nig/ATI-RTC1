<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Supplies;
use App\Exports\RsmiExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class RsmiController extends Controller
{
    // ── Shared helper: build flat rsmiItems + grouped structure ─────────
    private function buildItems($supplies)
    {
        $flat = $supplies->map(function ($supply) {
            return (object) [
                'issue_no'              => 'RSMI-' . now()->format('Y') . '-' . str_pad($supply->id, 4, '0', STR_PAD_LEFT),
                'responsibility_center' => $supply->category ?? '',
                'stock_no'              => $supply->id,
                'item'                  => $supply->name,
                'unit'                  => $supply->unit,
                'quantity_issued'       => $supply->quantity,
                'unit_cost'             => $supply->unit_price,
                'amount'                => $supply->unit_price * $supply->quantity,
            ];
        })->sortBy('stock_no')->values();

        // Group by stock_no — each group = one item type with multiple RIS rows
        $grouped = $flat->groupBy('stock_no')->map(function ($rows) {
            $rows    = $rows->values();
            $first   = $rows->first();
            $total_qty    = $rows->sum('quantity_issued');
            $total_amount = $rows->sum('amount');
            return [
                'stock_no'    => $first->stock_no,
                'item'        => $first->item,
                'unit'        => $first->unit,
                'unit_cost'   => $first->unit_cost,   // same unit cost for all rows of same stock
                'total_qty'   => $total_qty,
                'total_amount'=> $total_amount,
                'rows'        => $rows,                // individual RIS rows
            ];
        })->values();

        // Recap left: stock_no + total quantity per group
        $recapLeft = $grouped->map(fn($g) => [
            'stock_no' => $g['stock_no'],
            'quantity' => $g['total_qty'],
        ]);

        // Recap right: per-row matching recapLeft (unit_cost | total_cost | uacs)
        $recapRight = $grouped->map(fn($g) => [
            'unit_cost'  => $g['unit_cost'],
            'total_cost' => $g['total_amount'],
            'uacs_code'  => '',
        ]);

        return compact('flat', 'grouped', 'recapLeft', 'recapRight');
    }

    // ── Shared helper: apply filters to query ───────────────────────────
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('purchase_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
        if ($request->filled('description')) {
            $query->where('name', 'like', '%' . $request->description . '%');
        }
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->where('quantity', '>', 0);
            } elseif ($request->status === 'pending') {
                $query->where('quantity', '=', 0);
            }
        }
        return $query;
    }

    // ── Shared helper: build header array ───────────────────────────────
    private function buildHeader(Request $request, string $dateFormatted): array
    {
        return [
            'as_of'        => $request->query('as_of') ? Carbon::parse($request->query('as_of'))->format('F Y') : '',
            'entity_name'  => $request->query('entity_name',  ''),
            'fund_cluster' => $request->query('fund_cluster', ''),
            'serial_no'    => $request->query('serial_no',    ''),
            'date'         => $request->query('date') ? $dateFormatted : '',
            'supply_name'  => $request->query('f_supply_name', ''),
            'supply_role'  => $request->query('f_supply_role', ''),
            'acctg_name'   => $request->query('f_acctg_name',  ''),
            'acctg_date'   => $request->query('f_acctg_date')
                                ? Carbon::parse($request->query('f_acctg_date'))->format('F d, Y')
                                : '',
        ];
    }

    public function index(Request $request)
    {
        $query   = $this->applyFilters(Supplies::query(), $request);
        $supplies = $query->orderBy('id', 'asc')->get();

        ['flat' => $flat, 'grouped' => $grouped, 'recapLeft' => $recapLeft, 'recapRight' => $recapRight]
            = $this->buildItems($supplies);

        // Date range / filters strings (screen display only)
        $dateRange = '';
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateRange = 'From ' . Carbon::parse($request->date_from)->format('F d, Y') . ' to ' . Carbon::parse($request->date_to)->format('F d, Y');
        } elseif ($request->filled('date_from')) {
            $dateRange = 'From ' . Carbon::parse($request->date_from)->format('F d, Y');
        } elseif ($request->filled('date_to')) {
            $dateRange = 'Up to ' . Carbon::parse($request->date_to)->format('F d, Y');
        }
        $filters = [];
        if ($dateRange)                      $filters[] = 'Date Range: ' . $dateRange;
        if ($request->filled('description')) $filters[] = 'Description: ' . $request->description;
        // Status is intentionally excluded — report title always reads "Report of Supplies and Materials Issued"

        $dateVal       = $request->query('date', '');
        $dateFormatted = $dateVal
            ? (strpos($dateVal, ' ') !== false ? $dateVal : Carbon::parse($dateVal)->format('F d, Y'))
            : '';

        $header = $this->buildHeader($request, $dateFormatted);
        $header['date_range']      = $dateRange;
        $header['applied_filters'] = implode(', ', $filters);

        $descriptions = Supplies::whereNotNull('name')->where('name', '!=', '')->distinct()->pluck('name')->sort()->values();

        return view('client.report.rsmi.index', compact(
            'flat', 'grouped', 'recapLeft', 'recapRight', 'header', 'descriptions'
        ));
    }

    public function exportPDF(Request $request)
    {
        $query   = $this->applyFilters(Supplies::query(), $request);
        $supplies = $query->orderBy('id', 'asc')->get();

        ['flat' => $flat, 'grouped' => $grouped, 'recapLeft' => $recapLeft, 'recapRight' => $recapRight]
            = $this->buildItems($supplies);

        $dateVal       = $request->query('date', '');
        $dateFormatted = $dateVal
            ? (strpos($dateVal, ' ') !== false ? $dateVal : Carbon::parse($dateVal)->format('F d, Y'))
            : '';

        $header = $this->buildHeader($request, $dateFormatted);

        $pdf = Pdf::loadView('client.report.rsmi.pdf', compact(
            'flat', 'grouped', 'recapLeft', 'recapRight', 'header'
        ));
        return $pdf->download('rsmi_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new RsmiExport($request), 'rsmi_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}