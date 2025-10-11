<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class PropertyCardController extends Controller
{
    /**
     * Display a listing of property cards
     */
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by condition
        if ($request->has('condition') && $request->condition) {
            $query->byCondition($request->condition);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $equipment = $query->paginate(10);

        return view('client.propertycard.index', compact('equipment'));
    }

    /**
     * Display the specified property card
     */
    public function show($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('client.propertycard.show', compact('equipment'));
    }
}