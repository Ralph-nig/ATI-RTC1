<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class IcsController extends Controller
{
    /**
     * Display listing of ICS equipment (unit value < 50,000).
     */
    public function index(Request $request)
    {
        $query = Equipment::where('document_type', 'ICS')
                          ->where('unit_value', '<', 50000);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $sortBy        = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $equipment = $query->paginate(10);

        return view('client.report.ics.index', compact('equipment'));
    }

    /**
     * Show the printable ICS form for a single equipment item.
     */
    public function print(string $id)
    {
        $equipment = Equipment::where('document_type', 'ICS')
                              ->where('unit_value', '<', 50000)
                              ->findOrFail($id);

        return view('client.report.ics.print', compact('equipment'));
    }
}