<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Only allow admin users to access user management
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['user'] = User::all();
        return view('client.users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        User::create([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'password' => bcrypt($request['password']),
            'role'     => $request['role'] ?? 'user', // Add role support
        ]);

        return redirect()->route('users.index')->with('success', 'User has been successfully created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['user'] = User::find($id);
        return view('client.users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Add validation for the update
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'status' => 'nullable|in:active,inactive',
        ]);

        $user = User::find($id);
        
        if (!$user) {
            return redirect()->to('client/users')->with('error', 'User not found.');
        }
        
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->role = $request['role']; // Add role update
        
        // Add status update if provided
        if ($request->has('status')) {
            $user->status = $request['status'];
        }
        
        // Only update password if provided and not empty
        if (!empty($request['password'])) {
            $user->password = Hash::make($request['password']); // Use Hash::make instead of bcrypt
        }
        
        $user->save();

        // Redirect to users index instead of back
        return redirect()->to('client/users')->with('success', 'User information has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        
        // Prevent admin from deleting their own account
        if ($user->id === auth()->user()->id) {
            return response()->json(['error' => 'You cannot delete your own account.'], 403);
        }
        
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.'], 200);
    }
}   