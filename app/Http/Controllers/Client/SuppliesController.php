<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Supplies;
use Illuminate\Http\Request;

class SuppliesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplies::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by low stock
        if ($request->has('low_stock') && $request->low_stock == '1') {
            $query->lowStock();
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $supplies = $query->paginate(15);
        
        // Get unique categories for filter dropdown
        $categories = Supplies::distinct()->pluck('category')->filter();
        
        // Get summary statistics
        $stats = [
            'total_items' => Supplies::count(),
            'total_value' => Supplies::sum(\DB::raw('quantity * unit_price')),
            'low_stock_count' => Supplies::lowStock()->count(),
            'categories_count' => Supplies::distinct()->count('category')
        ];

        return view('client.supplies.index', compact('supplies', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Supplies::distinct()->pluck('category')->filter();
        $suppliers = Supplies::distinct()->pluck('supplier')->filter();
        
        return view('client.supplies.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'minimum_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        Supplies::create($validated);

        return redirect()->route('supplies.index')->with('success', 'Supply item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplies $supply)
    {
        return view('client.supplies.show', compact('supply'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplies $supply)
    {
        $categories = Supplies::distinct()->pluck('category')->filter();
        $suppliers = Supplies::distinct()->pluck('supplier')->filter();
        
        return view('client.supplies.edit', compact('supply', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplies $supply)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'minimum_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $supply->update($validated);

        return redirect()->route('supplies.index')->with('success', 'Supply item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplies $supply)
    {
        $supply->delete();

        return redirect()->route('supplies.index')->with('success', 'Supply item deleted successfully!');
    }

    /**
     * Bulk actions for supplies
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        switch ($action) {
            case 'delete':
                Supplies::whereIn('id', $ids)->delete();
                return response()->json(['success' => 'Selected items deleted successfully']);
                
            case 'update_category':
                $category = $request->category;
                Supplies::whereIn('id', $ids)->update(['category' => $category]);
                return response()->json(['success' => 'Category updated for selected items']);
                
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    /**
     * Export supplies data
     */
    public function export()
    {
        $supplies = Supplies::all();
        
        $filename = 'supplies_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($supplies) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'ID', 'Name', 'Description', 'Quantity', 'Unit Price', 'Unit', 
                'Category', 'Supplier', 'Purchase Date', 'Minimum Stock', 'Total Value', 'Notes'
            ]);
            
            // CSV data
            foreach ($supplies as $supply) {
                fputcsv($handle, [
                    $supply->id,
                    $supply->name,
                    $supply->description,
                    $supply->quantity,
                    $supply->unit_price,
                    $supply->unit,
                    $supply->category,
                    $supply->supplier,
                    $supply->purchase_date?->format('Y-m-d'),
                    $supply->minimum_stock,
                    $supply->total_value,
                    $supply->notes
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }
}