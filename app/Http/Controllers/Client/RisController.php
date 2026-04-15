<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ris;
use App\Models\Supplies;
use App\Models\StockMovement;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RisController extends Controller
{
    /**
     * Show all RIS requests by the logged-in requestor.
     */
    public function index(Request $request)
    {
        $query = Ris::with(['supplies', 'requester']);

        // Non-admins only see their own requests
        if (!auth()->user()->isAdmin()) {
            $query->where('requested_by', Auth::id());
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'like', "%{$request->reference}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('purpose', 'like', "%{$request->search}%");
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100])
            ? (int) $request->per_page
            : 10;

        $risRequests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('client.ris.index', compact('risRequests'));
    }

    /**
     * Show the RIS creation form.
     */
    public function create()
    {
        $supplies = Supplies::orderBy('name')->get();
        return view('client.ris.create', compact('supplies'));
    }

    /**
     * Store a new RIS request and notify all admins.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purpose'              => 'required|string|max:255',
            'date_needed'          => 'nullable|date|after_or_equal:today',
            'notes'                => 'nullable|string|max:1000',
            'supplies'             => 'required|array|min:1',
            'supplies.*.supply_id' => 'required|exists:supplies,id',
            'supplies.*.quantity'  => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $ris = Ris::create([
                'purpose'      => $validated['purpose'],
                'date_needed'  => $validated['date_needed'] ?? null,
                'notes'        => $validated['notes'] ?? null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
                'reference'    => Ris::generateReference(),
            ]);

            foreach ($request->supplies as $item) {
                $ris->supplies()->attach($item['supply_id'], [
                    'quantity_requested' => $item['quantity'],
                    'status'             => 'pending',
                ]);
            }

            // ── Notify all admins ──────────────────────────────────────────
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type'    => 'ris_request',
                    'title'   => 'New RIS Request',
                    'message' => Auth::user()->name . ' submitted a new supply request: ' . $validated['purpose'],
                    'data'    => [
                        'ris_id'         => $ris->id,
                        'ris_reference'  => $ris->reference,
                        'requestor_name' => Auth::user()->name,
                        'purpose'        => $validated['purpose'],
                        'item_count'     => count($request->supplies),
                    ],
                ]);
            }

            DB::commit();
            return redirect()->route('client.ris.index')
                ->with('success', 'RIS submitted successfully! Reference: ' . $ris->reference);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RIS store failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit RIS: ' . $e->getMessage());
        }
    }

    /**
     * View a single RIS.
     * When an admin opens a pending RIS, any item whose current stock is less
     * than the requested quantity is automatically marked as rejected so the
     * admin doesn't accidentally approve something that can't be fulfilled.
     */
    public function show(string $id)
    {
        $ris = Ris::with(['supplies', 'requester', 'approver'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $ris->requested_by !== Auth::id()) {
            abort(403);
        }

        // Auto-reject items with insufficient stock (admin view, pending RIS only)
        $autoRejected = [];
        if (auth()->user()->isAdmin() && $ris->status === 'pending') {
            foreach ($ris->supplies as $supply) {
                if (
                    $supply->pivot->status === 'pending' &&
                    $supply->quantity < $supply->pivot->quantity_requested
                ) {
                    $ris->supplies()->updateExistingPivot($supply->id, [
                        'status' => 'rejected',
                    ]);
                    $autoRejected[] = $supply->name;
                }
            }

            // Reload relationship so the view sees the updated pivot statuses
            if (!empty($autoRejected)) {
                $ris->load('supplies');
            }
        }

        return view('client.ris.show', compact('ris', 'autoRejected'));
    }

    /**
     * Print view.
     */
    public function print(string $id)
    {
        $ris = Ris::with(['supplies', 'requester', 'approver'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $ris->requested_by !== Auth::id()) {
            abort(403);
        }

        return view('client.ris.print', compact('ris'));
    }

    /**
     * Approve a RIS — deducts stock and notifies requestor.
     * Respects individual item statuses: skips items already rejected via rejectItem().
     * If all items were pre-rejected, the RIS becomes 'rejected' instead of 'approved'.
     */
    public function approve(Request $request, string $id)
    {
        $ris = Ris::with(['supplies', 'requester'])->findOrFail($id);

        if ($ris->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'RIS is no longer pending.'], 400);
        }

        DB::beginTransaction();
        try {
            $anyApproved = false;

            foreach ($ris->supplies as $supply) {
                // Skip items that were individually rejected via rejectItem()
                if ($supply->pivot->status === 'rejected') {
                    continue;
                }

                $qty = $supply->pivot->quantity_requested;

                if ($supply->quantity < $qty) {
                    throw new \Exception("Insufficient stock for {$supply->name}. Available: {$supply->quantity}, Requested: {$qty}");
                }

                $newQty = $supply->quantity - $qty;
                DB::table('supplies')->where('id', $supply->id)->update(['quantity' => $newQty]);

                $movement = new StockMovement();
                $movement->supply_id          = $supply->id;
                $movement->type               = 'out';
                $movement->quantity           = $qty;
                $movement->balance_after      = $newQty;
                $movement->reference          = $ris->reference;
                $movement->notes              = "RIS approved: {$ris->purpose}";
                $movement->office_description = $ris->purpose;
                $movement->save();

                $ris->supplies()->updateExistingPivot($supply->id, [
                    'status'          => 'approved',
                    'quantity_issued' => $qty,
                    'issued_at'       => now(),
                ]);

                $anyApproved = true;
            }

            $finalStatus = $anyApproved ? 'approved' : 'rejected';

            $ris->update([
                'status'      => $finalStatus,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // ── Notify requestor ──────────────────────────────────────────
            Notification::create([
                'user_id' => $ris->requested_by,
                'type'    => $anyApproved ? 'ris_approved' : 'ris_rejected',
                'title'   => $anyApproved ? 'RIS Request Approved' : 'RIS Request Rejected',
                'message' => $anyApproved
                    ? 'Your supply request "' . $ris->purpose . '" (' . $ris->reference . ') has been approved and stock has been issued.'
                    : 'Your supply request "' . $ris->purpose . '" (' . $ris->reference . ') has been rejected.',
                'data'    => [
                    'ris_id'        => $ris->id,
                    'ris_reference' => $ris->reference,
                    'approved_by'   => Auth::user()->name,
                ],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'RIS approved and stock deducted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Approve a single supply item on a pending RIS.
     */
    public function approveItem(string $risId, string $supplyId)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $ris = Ris::findOrFail($risId);
        if ($ris->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'RIS is no longer pending.'], 400);
        }

        $ris->supplies()->updateExistingPivot($supplyId, ['status' => 'approved']);

        return response()->json(['success' => true]);
    }

    /**
     * Reject a single supply item on a pending RIS.
     */
    public function rejectItem(string $risId, string $supplyId)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $ris = Ris::findOrFail($risId);
        if ($ris->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'RIS is no longer pending.'], 400);
        }

        $ris->supplies()->updateExistingPivot($supplyId, ['status' => 'rejected']);

        return response()->json(['success' => true]);
    }

    /**
     * Finalize a RIS after all items have been actioned individually.
     * Approved items get stock deducted; rejected items are skipped.
     * RIS status becomes 'approved' if any item was approved, 'rejected' if all rejected.
     */
    public function finalize(string $id)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $ris = Ris::with(['supplies', 'requester'])->findOrFail($id);

        if ($ris->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'RIS is no longer pending.'], 400);
        }

        // All items must be actioned before finalizing
        $stillPending = $ris->supplies->filter(fn($s) => $s->pivot->status === 'pending');
        if ($stillPending->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please action all items before finalizing.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $anyApproved = false;

            foreach ($ris->supplies as $supply) {
                if ($supply->pivot->status !== 'approved') continue;

                $qty = $supply->pivot->quantity_requested;

                if ($supply->quantity < $qty) {
                    throw new \Exception("Insufficient stock for {$supply->name}. Available: {$supply->quantity}, Requested: {$qty}");
                }

                $newQty = $supply->quantity - $qty;
                DB::table('supplies')->where('id', $supply->id)->update(['quantity' => $newQty]);

                $movement = new StockMovement();
                $movement->supply_id          = $supply->id;
                $movement->type               = 'out';
                $movement->quantity           = $qty;
                $movement->balance_after      = $newQty;
                $movement->reference          = $ris->reference;
                $movement->notes              = "RIS finalized: {$ris->purpose}";
                $movement->office_description = $ris->purpose;
                $movement->save();

                $ris->supplies()->updateExistingPivot($supply->id, [
                    'quantity_issued' => $qty,
                    'issued_at'       => now(),
                ]);

                $anyApproved = true;
            }

            $finalStatus = $anyApproved ? 'approved' : 'rejected';

            $ris->update([
                'status'      => $finalStatus,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Notify requestor
            Notification::create([
                'user_id' => $ris->requested_by,
                'type'    => $anyApproved ? 'ris_approved' : 'ris_rejected',
                'title'   => $anyApproved ? 'RIS Request Approved' : 'RIS Request Rejected',
                'message' => $anyApproved
                    ? 'Your supply request "' . $ris->purpose . '" (' . $ris->reference . ') has been processed and approved items have been issued.'
                    : 'Your supply request "' . $ris->purpose . '" (' . $ris->reference . ') has been rejected.',
                'data'    => [
                    'ris_id'        => $ris->id,
                    'ris_reference' => $ris->reference,
                    'link'          => route('client.ris.show', $ris->id),
                    'actioned_by'   => Auth::user()->name,
                ],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'RIS finalized successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Reject a RIS and notify requestor.
     */
    public function reject(Request $request, string $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $ris = Ris::with('requester')->findOrFail($id);

        if ($ris->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'RIS is no longer pending.'], 400);
        }

        $ris->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->reason,
        ]);

        // ── Notify requestor ──────────────────────────────────────────────
        Notification::create([
            'user_id' => $ris->requested_by,
            'type'    => 'ris_rejected',
            'title'   => 'RIS Request Rejected',
            'message' => 'Your supply request "' . $ris->purpose . '" (' . $ris->reference . ') has been rejected.'
                       . ($request->reason ? ' Reason: ' . $request->reason : ''),
            'data'    => [
                'ris_id'        => $ris->id,
                'ris_reference' => $ris->reference,
                'rejected_by'   => Auth::user()->name,
                'reason'        => $request->reason,
            ],
        ]);

        return response()->json(['success' => true, 'message' => 'RIS rejected.']);
    }
}