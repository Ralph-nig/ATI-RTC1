<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Mail\UserCredentialMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized access. Admin privileges required.'], 403);
                }
                abort(403, 'Unauthorized access. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $data['user'] = User::all();
        return view('client.users.index', $data);
    }

    public function create()
    {
        return view('client.users.create');
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        $plainPassword = $validated['password'];

        if ($validated['role'] === 'admin') {
            $validated['can_create']    = true;
            $validated['can_read']      = true;
            $validated['can_update']    = true;
            $validated['can_delete']    = true;
            $validated['can_stock_in']  = true;
            $validated['can_stock_out'] = true;
            $validated['can_request']   = false;
        } elseif ($validated['role'] === 'requestor') {
            $validated['can_create']    = false;
            $validated['can_read']      = false;
            $validated['can_update']    = false;
            $validated['can_delete']    = false;
            $validated['can_stock_in']  = false;
            $validated['can_stock_out'] = false;
            $validated['can_request']   = true;
        } else {
            $validated['can_create']    = $request->boolean('can_create', false);
            $validated['can_read']      = $request->boolean('can_read', true);
            $validated['can_update']    = $request->boolean('can_update', false);
            $validated['can_delete']    = $request->boolean('can_delete', false);
            $validated['can_stock_in']  = $request->boolean('can_stock_in', false);
            $validated['can_stock_out'] = $request->boolean('can_stock_out', false);
            $validated['can_request']   = false;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status']   = $validated['status'] ?? 'active';

        $user = User::create($validated);

        try {
            Mail::to($user->email)->send(new UserCredentialMail($user, $plainPassword));
            return redirect()->route('users.index')
                ->with('success', 'User created and credentials sent to their email!');
        } catch (\Exception $e) {
            Log::error('Failed to send credential email: ' . $e->getMessage());
            return redirect()->route('users.index')
                ->with('warning', 'User created, but failed to send credentials email. Please share manually.');
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data['user'] = User::findOrFail($id);
        return view('client.users.edit', $data);
    }

    public function update(UserRequest $request, string $id)
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);

        $user->name   = $validated['name'];
        $user->email  = $validated['email'];
        $user->role   = $validated['role'];
        $user->status = $validated['status'] ?? 'active';

        if ($validated['role'] === 'admin') {
            $user->can_create    = true;
            $user->can_read      = true;
            $user->can_update    = true;
            $user->can_delete    = true;
            $user->can_stock_in  = true;
            $user->can_stock_out = true;
            $user->can_request   = false;
        } elseif ($validated['role'] === 'requestor') {
            $user->can_create    = false;
            $user->can_read      = false;
            $user->can_update    = false;
            $user->can_delete    = false;
            $user->can_stock_in  = false;
            $user->can_stock_out = false;
            $user->can_request   = true;
        } else {
            $user->can_create    = $request->boolean('can_create', false);
            $user->can_read      = $request->boolean('can_read', true);
            $user->can_update    = $request->boolean('can_update', false);
            $user->can_delete    = $request->boolean('can_delete', false);
            $user->can_stock_in  = $request->boolean('can_stock_in', false);
            $user->can_stock_out = $request->boolean('can_stock_out', false);
            $user->can_request   = false;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User information updated successfully!');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->user()->id) {
            return response()->json(['error' => 'You cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.'], 200);
    }

    /**
     * Display the organisational chart.
     */
    public function orgchart()
    {
        $allUsers = User::all();

        // Build head map directly from users table: ['pme' => 7, 'admin' => 3, ...]
        $orgHeads = $allUsers
            ->where('is_section_head', true)
            ->whereNotNull('org_unit')
            ->pluck('id', 'org_unit')
            ->toArray();

        return view('client.users.orgchart', [
            'user'     => $allUsers,
            'orgHeads' => $orgHeads,
        ]);
    }

    /**
     * Assign (or clear) the section head for a given unit.
     *
     * POST /users/orgchart/assign-head
     * Body: { unit: 'pme', user_id: 5 }   (user_id null/empty = clear)
     *
     * Rules:
     *  - Only 1 head per unit (previous head in same unit is demoted).
     *  - A user cannot be head of two different units simultaneously.
     */
    public function assignHead(Request $request)
    {
        $request->validate([
            'unit'    => ['required', 'string', 'in:ocd,pme,admin,cdm,pas,iss'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $unit   = $request->input('unit');
        $userId = $request->input('user_id') ?: null;

        // Collect IDs of every current head for this unit so JS can strip badges
        $demotedIds = User::where('is_section_head', true)
            ->where('org_unit', $unit)
            ->pluck('id')
            ->toArray();

        // Demote them all first
        User::whereIn('id', $demotedIds)
            ->update(['is_section_head' => false]);

        // Clear only — no new head requested
        if (!$userId) {
            return response()->json([
                'message'     => 'Section head cleared for ' . strtoupper($unit) . '.',
                'unit'        => $unit,
                'user_id'     => null,
                'demoted_ids' => $demotedIds,
            ]);
        }

        // Guard: user is already head of a DIFFERENT unit — roll back and reject
        $conflict = User::where('id', $userId)
            ->where('is_section_head', true)
            ->where('org_unit', '!=', $unit)
            ->first();

        if ($conflict) {
            User::whereIn('id', $demotedIds)->update(['is_section_head' => true]);
            return response()->json([
                'error' => $conflict->name . ' is already the head of '
                         . strtoupper($conflict->org_unit)
                         . '. Remove them there first.',
            ], 422);
        }

        // Promote new head — stamp org_unit in case it was null/different
        $user = User::findOrFail($userId);
        $user->org_unit        = $unit;
        $user->is_section_head = true;
        $user->save();

        return response()->json([
            'message'     => $user->name . ' is now the section head of ' . strtoupper($unit) . '.',
            'unit'        => $unit,
            'user_id'     => (int) $userId,
            'demoted_ids' => $demotedIds,
        ]);
    }


    /**
     * Get equipment assigned to a user (responsible_person matches user name).
     */
    public function getEquipment(string $id)
    {
        $user = User::findOrFail($id);

        $equipment = \App\Models\Equipment::where('responsible_person', $user->name)
            ->orderBy('article')
            ->get([
                'id', 'property_number', 'article', 'classification',
                'condition', 'responsibility_center', 'unit_value', 'acquisition_date'
            ]);

        return response()->json(['equipment' => $equipment]);
    }
}