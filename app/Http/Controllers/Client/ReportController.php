<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('client.report.index');
    }

    // RSMI Report
    public function rsmi(Request $request)
    {
        $rsmiData = collect();  
        
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $department = $request->input('department');
        $status = $request->input('status');
        
        return view('client.report.rsmi.index', compact('rsmiData'));
    }

    // RPCI Report
    public function rpci(Request $request)
    {
        return view('client.report.rpci.index');
    }

    // PPES Report
    public function ppes(Request $request)
    {
        return view('client.report.ppes.index');
    }

    // RPC PPE Report
    public function rpcPpe(Request $request)
    {
        // Get all equipment, ordered by classification and article
        $equipmentQuery = Equipment::orderBy('classification')
            ->orderBy('article')
            ->orderBy('property_number');

        // Apply filters if provided
        if ($request->filled('classification')) {
            $equipmentQuery->where('classification', $request->classification);
        }

        if ($request->filled('condition')) {
            $equipmentQuery->where('condition', $request->condition);
        }

        if ($request->filled('date_from')) {
            $equipmentQuery->whereDate('acquisition_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $equipmentQuery->whereDate('acquisition_date', '<=', $request->date_to);
        }

        $allEquipment = $equipmentQuery->get();

        // Group equipment by classification
        $groupedEquipment = $allEquipment->groupBy('classification');

        // Get all unique classifications for filter dropdown
        $classifications = Equipment::whereNotNull('classification')
            ->where('classification', '!=', '')
            ->distinct()
            ->pluck('classification')
            ->sort()
            ->values();

        // Calculate totals
        $totalValue = $allEquipment->sum('unit_value');
        $totalItems = $allEquipment->count();
        $serviceableCount = $allEquipment->where('condition', 'Serviceable')->count();
        $unserviceableCount = $allEquipment->where('condition', 'Unserviceable')->count();

        return view('client.report.rpc-ppe.index', compact(
            'groupedEquipment',
            'classifications',
            'totalValue',
            'totalItems',
            'serviceableCount',
            'unserviceableCount'
        ));
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}