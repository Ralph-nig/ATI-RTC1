<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PpesExport;

class PpesController extends Controller
{
    /**
     * Build the mapped disposal data for a single equipment item.
     * Extracted to avoid duplicating the same logic in index() and exportPDF().
     */
    private function mapEquipmentItem(Equipment $equipment): object
    {
        $disposalMethods = ['sale', 'transfer', 'destruction', 'others'];

        // Normalize casing so 'Sale', 'SALE', 'sale' all match
        $method      = strtolower($equipment->disposal_method ?? '');
        $hasDisposal = in_array($method, $disposalMethods);

        return (object) [
            'date_acquired'                 => $equipment->acquisition_date?->format('m/d/Y') ?? '---',
            'particulars_articles'          => $equipment->article . ' - ' . $equipment->description,
            'property_no'                   => $equipment->property_number ?: '---',
            'qty'                           => $equipment->quantity ?? 1,
            'unit_cost'                     => $equipment->unit_value,
            'total_cost'                    => $equipment->unit_value,
            'accumulated_depreciation'      => 0,
            'accumulated_impairment_losses' => 0,
            'carrying_amount'               => $equipment->unit_value,
            'remarks'                       => $equipment->condition ?: '---',
            // Disposal columns — populated based on which method was chosen
            'sale'                          => $method === 'sale'        ? $equipment->unit_value       : '',
            'transfer'                      => $method === 'transfer'    ? $equipment->unit_value       : '',
            'destruction'                   => $method === 'destruction' ? $equipment->unit_value       : '',
            'others'                        => $method === 'others'      ? $equipment->disposal_details : '',
            'total_disposal'                => $hasDisposal ? $equipment->unit_value : '',
            'appraised_value'               => $hasDisposal ? $equipment->unit_value : '',
            // Record of Sales
            'or_no'                         => '',
            'amount'                        => '',
        ];
    }

    /**
     * Build a base query with shared filters applied.
     */
    private function buildQuery(Request $request)
    {
        $query = Equipment::query();

        $query->where('condition', 'Unserviceable');

        if ($request->filled('date_from')) {
            $query->whereDate('acquisition_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('acquisition_date', '<=', $request->date_to);
        }
        if ($request->filled('classification')) {
            $query->where('article', 'like', '%' . $request->classification . '%');
        }

        return $query->orderBy('acquisition_date', 'desc');
    }

    /**
     * Build the header array from request inputs.
     */
    private function buildHeader(Request $request): array
    {
        return [
            'as_of'              => $request->input('as_of')
                ? \Carbon\Carbon::parse($request->input('as_of'))->format('F d, Y')
                : '',
            'entity_name'        => $request->input('entity_name') ?: '',
            'fund_cluster'       => $request->input('fund_cluster') ?: '',
            'accountable_person' => $request->input('accountable_person') ?: '',
            'position'           => $request->input('position') ?: '',
            'office'             => $request->input('office') ?: '',
            'assumption_date'    => $request->input('assumption_date') ?: '',
        ];
    }

    public function index(Request $request)
    {
        $ppesItems = $this->buildQuery($request)
            ->get()
            ->map(fn (Equipment $equipment) => $this->mapEquipmentItem($equipment));

        // Get all unique articles for filter dropdown
        $classifications = Equipment::whereNotNull('article')
            ->where('article', '!=', '')
            ->distinct()
            ->pluck('article')
            ->sort()
            ->values();

        return view('client.report.ppes.index', [
            'ppesItems'       => $ppesItems,
            'classifications' => $classifications,
            'filters'         => $request->all(),
            'header'          => $this->buildHeader($request),
        ]);
    }

    public function exportPDF(Request $request)
    {
        $ppesItems = $this->buildQuery($request)
            ->get()
            ->map(fn (Equipment $equipment) => $this->mapEquipmentItem($equipment));

        $pdf = Pdf::loadView('client.report.ppes.pdf', [
            'ppesItems' => $ppesItems,
            'filters'   => $request->all(),
            'header'    => $this->buildHeader($request),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('PPES_Report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PpesExport($request),
            'PPES_Report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}