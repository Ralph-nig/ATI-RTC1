<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Exports\IirupExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class IirupController extends Controller
{
    // ─────────────────────────────────────────────
    //  Shared helpers
    // ─────────────────────────────────────────────

    private function buildQuery(Request $request)
    {
        $query = Equipment::query()->where('condition', 'Unserviceable');

        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('classification', 'like', '%' . $request->classification . '%');
        }

        $disposedFilter = $request->get('disposed', 'all');

        if ($disposedFilter === 'disposed') {
            $query->where(function ($q) {
                $q->whereNotNull('sale')->where('sale', '!=', '')
                  ->orWhereNotNull('transfer')->where('transfer', '!=', '')
                  ->orWhereNotNull('destruction')->where('destruction', '!=', '')
                  ->orWhereNotNull('others')->where('others', '!=', '')
                  ->orWhereNotNull('appraised_value')->where('appraised_value', '!=', '')
                  ->orWhereNotNull('or_no')->where('or_no', '!=', '')
                  ->orWhereNotNull('amount')->where('amount', '!=', '');
            });
        } elseif ($disposedFilter === 'not_disposed') {
            $query->where(function ($q) {
                $q->where(fn($s) => $s->whereNull('sale')->orWhere('sale', '=', ''))
                  ->where(fn($s) => $s->whereNull('transfer')->orWhere('transfer', '=', ''))
                  ->where(fn($s) => $s->whereNull('destruction')->orWhere('destruction', '=', ''))
                  ->where(fn($s) => $s->whereNull('others')->orWhere('others', '=', ''))
                  ->where(fn($s) => $s->whereNull('appraised_value')->orWhere('appraised_value', '=', ''))
                  ->where(fn($s) => $s->whereNull('or_no')->orWhere('or_no', '=', ''))
                  ->where(fn($s) => $s->whereNull('amount')->orWhere('amount', '=', ''));
            });
        }

        return $query;
    }

    private function mapEquipment($equipment): object
    {
        $unitValue = (float)($equipment->unit_value ?? 0);

        return (object) [
            'date_acquired'                 => $equipment->acquisition_date
                                                ? $equipment->acquisition_date->format('m/d/Y')
                                                : '---',
            'particulars_articles'          => $equipment->article
                                                . ($equipment->description ? ' - ' . $equipment->description : ''),
            'property_no'                   => $equipment->property_number ?: '---',
            'qty'                           => 1,
            'unit_cost'                     => $unitValue,
            'total_cost'                    => $unitValue,
            'accumulated_depreciation'      => round($unitValue * 0.95, 2),
            'accumulated_impairment_losses' => ($equipment->accumulated_impairment_losses ?? 0) ?: 0,
            'carrying_amount'               => round($unitValue * 0.05, 2),
            'remarks'                       => $equipment->condition ?: '',
            'sale'                          => $equipment->sale ?? '',
            'transfer'                      => $equipment->transfer ?? '',
            'destruction'                   => $equipment->destruction ?? '',
            'others'                        => $equipment->others ?? '',
            'total_disposal'                => $equipment->total_disposal ?? '',
            'appraised_value'               => $equipment->appraised_value ?? 0,
            'or_no'                         => $equipment->or_no ?? '',
            'amount'                        => $equipment->amount ?? 0,
        ];
    }

    private function buildHeader(Request $request): array
    {
        return [
            // Header fields
            'as_of'              => $request->query('as_of')
                                    ? Carbon::parse($request->as_of)->format('F d, Y')
                                    : '',
            'entity_name'        => $request->query('entity_name', ''),
            'fund_cluster'       => $request->query('fund_cluster', ''),
            'accountable_person' => $request->query('accountable_person', ''),
            'position'           => $request->query('position', ''),
            'office'             => $request->query('office', ''),

            // Footer / signature fields
            'f_req_name'         => $request->query('f_req_name',  'FRANKLIN A. SALCEDO'),
            'f_req_role'         => $request->query('f_req_role',  'Supply and Property Officer'),
            'f_appr_name'        => $request->query('f_appr_name', 'JAYVEE BRYAN G. CARILLO, Ph.D.'),
            'f_appr_role'        => $request->query('f_appr_role', 'Center Director'),
            'f_insp_name'        => $request->query('f_insp_name', 'JOSE O. KANLAS, JR.'),
            'f_insp_role'        => $request->query('f_insp_role', 'Inspection Officer'),
            'f_aud_name'         => $request->query('f_aud_name',  'JELANIE S. WANAWAN'),
            'f_aud_role'         => $request->query('f_aud_role',  'State Auditor II'),
            'f_aud_role2'        => $request->query('f_aud_role2', 'OIC - Audit Team Leader'),
        ];
    }

    // ─────────────────────────────────────────────
    //  Actions
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $ppesItems = $this->buildQuery($request)
            ->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(fn($e) => $this->mapEquipment($e));

        $classifications = Equipment::whereNotNull('classification')
            ->distinct()
            ->pluck('classification')
            ->sort();

        $header = $this->buildHeader($request);

        return view('client.report.iirup.index', compact('ppesItems', 'header', 'classifications'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new IirupExport($request),
            'iirup_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPDF(Request $request)
    {
        $ppesItems = $this->buildQuery($request)
            ->orderBy('acquisition_date', 'desc')
            ->get()
            ->map(fn($e) => $this->mapEquipment($e));

        $header = $this->buildHeader($request);

        $pdf = Pdf::loadView('client.report.iirup.pdf', [
            'ppesItems' => $ppesItems,
            'header'    => $header,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('iirup_report_' . now()->format('Y-m-d') . '.pdf');
    }
}