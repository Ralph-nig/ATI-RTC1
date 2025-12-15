<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DeletedEquipment;
use App\Models\Equipment;
use Illuminate\Http\Request;

class DeletedEquipmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted equipment.');
        }

        $query = DeletedEquipment::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('condition') && !empty($request->condition)) {
            $query->where('condition', $request->condition);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->byUser($request->user_id);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deletedItems = $query->orderBy('created_at', 'desc')->paginate(15);
        $conditions = ['Serviceable', 'Unserviceable'];
        $users = \App\Models\User::all();

        return view('client.deleted-equipment.index', compact('deletedItems', 'conditions', 'users'));
    }

    public function restore($id)
    {
        if (!auth()->user()->hasPermission('create')) {
            return redirect()->back()->with('error', 'You do not have permission to restore equipment.');
        }

        $deletedItem = DeletedEquipment::findOrFail($id);

        // Check if property number already exists (including soft deleted)
        $existingEquipment = Equipment::withTrashed()
            ->where('property_number', $deletedItem->property_number)
            ->first();
        
        if ($existingEquipment) {
            return redirect()->back()->with('error', 'Cannot restore: Equipment with property number "' . $deletedItem->property_number . '" already exists in the system.');
        }

        Equipment::create([
            'property_number' => $deletedItem->property_number,
            'article' => $deletedItem->article,
            'classification' => $deletedItem->classification,
            'description' => $deletedItem->description,
            'unit_of_measurement' => $deletedItem->unit_of_measurement,
            'unit_value' => $deletedItem->unit_value,
            'condition' => $deletedItem->condition,
            'acquisition_date' => $deletedItem->acquisition_date,
            'location' => $deletedItem->location,
            'responsible_person' => $deletedItem->responsible_person,
            'remarks' => $deletedItem->remarks
        ]);

        $deletedItem->delete();

        return redirect()->route('deleted-equipment.index')
            ->with('success', 'Equipment restored successfully!');
    }

    public function permanentDelete($id)
    {
        if (!auth()->user()->hasPermission('delete')) {
            return redirect()->back()->with('error', 'You do not have permission to permanently delete equipment.');
        }

        $deletedItem = DeletedEquipment::findOrFail($id);
        $deletedItem->forceDelete();

        return redirect()->route('deleted-equipment.index')
            ->with('success', 'Equipment permanently deleted!');
    }

    public function show($id)
    {
        if (!auth()->user()->hasPermission('read')) {
            abort(403, 'You do not have permission to view deleted equipment.');
        }

        $deletedItem = DeletedEquipment::with('user')->findOrFail($id);
        return view('client.deleted-equipment.show', compact('deletedItem'));
    }
}