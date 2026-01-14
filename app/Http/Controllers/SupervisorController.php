<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    /**
     * Display a listing of supervisors.
     */
    public function index()
    {
        $supervisors = User::where('role', 'supervisor')->get();
        $bagians = Bagian::all();
        
        return view('admin.kelolasupervisor', compact('supervisors', 'bagians'));
    }

    /**
     * Store a newly created supervisor.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'nama_bagian' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // Create User with supervisor role
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'supervisor',
            ]);

            // Create Bagian linked to supervisor's email
            Bagian::create([
                'nama_bagian' => $validated['nama_bagian'],
                'email' => $validated['email'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor dan bagian berhasil ditambahkan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supervisor detail (for edit modal).
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Get bagian linked to this supervisor's email
            $bagian = Bagian::where('email', $user->email)->first();

            if ($user->role !== 'supervisor') {
                return response()->json([
                    'success' => false,
                    'message' => 'User bukan supervisor'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->email,
                    'email' => $user->email,
                    'bagian' => $bagian ? [
                        'id_bagian' => $bagian->id_bagian,
                        'nama_bagian' => $bagian->nama_bagian
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update supervisor information.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->role !== 'supervisor') {
                return response()->json([
                    'success' => false,
                    'message' => 'User bukan supervisor'
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->email . ',email',
                'password' => 'nullable|min:8|confirmed',
                'nama_bagian' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // If email changed, update user and bagian
            if ($validated['email'] !== $user->email) {
                // Update user email
                $user->email = $validated['email'];
                
                // Update bagian email
                $bagian = Bagian::where('email', $user->getOriginal('email'))->first();
                if ($bagian) {
                    $bagian->email = $validated['email'];
                    $bagian->save();
                }
            }

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // Update bagian nama_bagian
            $bagian = Bagian::where('email', $user->email)->first();
            if ($bagian) {
                $bagian->nama_bagian = $validated['nama_bagian'];
                $bagian->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete supervisor.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->role !== 'supervisor') {
                return response()->json([
                    'success' => false,
                    'message' => 'User bukan supervisor'
                ], 403);
            }

            DB::beginTransaction();

            // Delete bagian linked to this supervisor
            Bagian::where('email', $user->email)->delete();

            // Delete user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor dan bagiannya berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
