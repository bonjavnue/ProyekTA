<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        
        return view('admin.kelolaadmin', compact('admins'));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil ditambahkan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin information.
     */
    public function update(Request $request, $email)
    {
        try {
            $admin = User::findOrFail($email);

            if ($admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User bukan admin'
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email,' . $email . ',email',
                'password' => 'nullable|min:8|confirmed',
            ]);

            // Update email if changed
            if ($validated['email'] !== $admin->email) {
                $admin->email = $validated['email'];
            }

            // Update password if provided
            if ($request->filled('password')) {
                $admin->password = Hash::make($validated['password']);
            }

            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete admin.
     */
    public function destroy($email)
    {
        try {
            $admin = User::findOrFail($email);

            if ($admin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User bukan admin'
                ], 403);
            }

            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
