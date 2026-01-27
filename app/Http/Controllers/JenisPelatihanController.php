<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use App\Http\Requests\JenisPelatihanRequest;

class JenisPelatihanController extends Controller
{
    /**
     * Generate ID Jenis Pelatihan dengan format TRN000, TRN001, dst
     */
    private function generateId()
    {
        $lastId = JenisPelatihan::orderBy('id_jenis', 'desc')->first();
        
        if (!$lastId) {
            // Jika tidak ada data, mulai dari TRN000
            return 'TRN000';
        }

        // Extract nomor dari ID terakhir (misal: TRN005 -> 005)
        $lastNumber = intval(substr($lastId->id_jenis, 3));
        $nextNumber = $lastNumber + 1;

        // Format dengan leading zeros (3 digit)
        return 'TRN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    /**
     * Display a listing of the resource with search and pagination.
     */
    public function index()
    {
        $search = request('search', '');
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'asc');
        $perPage = request('per_page', 10);

        // Validasi per_page - hanya terima nilai yang diizinkan
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Validasi sort_by - hanya terima kolom yang valid
        $allowedSortBy = ['id_jenis', 'nama_jenis', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validasi sort_order
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $query = JenisPelatihan::query();

        // Apply search filter
        if ($search) {
            $query->search($search);
        }
        // Apply sorting dengan parameter dinamis
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results with dynamic per_page
        $jenisPelatihans = $query->paginate($perPage)->appends(request()->query());

        return view('admin.jenispelatihan', compact('jenisPelatihans', 'search', 'sortBy', 'sortOrder', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jenis-pelatihan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JenisPelatihanRequest $request)
    {
        try {
            // Validasi - hapus id_jenis dari request karena akan auto-generate
            $data = $request->validated();
            $data['id_jenis'] = $this->generateId();

            // Simpan ke database
            $jenisPelatihan = JenisPelatihan::create($data);

            // Jika AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Pelatihan berhasil ditambahkan',
                    'data' => $jenisPelatihan
                ], 201);
            }

            return redirect()->route('jenis-pelatihan.index')
                            ->with('success', 'Jenis Pelatihan berhasil ditambahkan');
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan jenis pelatihan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'Gagal menambahkan jenis pelatihan: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisPelatihan $jenisPelatihan)
    {
        return view('jenis-pelatihan.show', compact('jenisPelatihan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisPelatihan $jenisPelatihan)
    {
        return view('jenis-pelatihan.edit', compact('jenisPelatihan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JenisPelatihanRequest $request, JenisPelatihan $jenisPelatihan)
    {
        try {
            // Update data
            $jenisPelatihan->update($request->validated());

            // Jika AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Pelatihan berhasil diperbarui',
                    'data' => $jenisPelatihan
                ], 200);
            }

            return redirect()->route('jenis-pelatihan.index')
                            ->with('success', 'Jenis Pelatihan berhasil diperbarui');
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui jenis pelatihan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'Gagal memperbarui jenis pelatihan: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPelatihan $jenisPelatihan)
    {
        try {
            // Check if jenis pelatihan has related jadwal pelatihan
            if ($jenisPelatihan->JadwalPelatihan()->exists()) {
                $message = 'Tidak dapat menghapus jenis pelatihan ini karena masih terdapat jadwal pelatihan yang terhubung. Hapus jadwal pelatihan terlebih dahulu.';
                
                // Jika AJAX request, return JSON
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return redirect()->route('jenis-pelatihan.index')
                                ->with('error', $message);
            }

            $jenisPelatihan->delete();

            // Jika AJAX request, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis Pelatihan berhasil dihapus'
                ], 200);
            }

            return redirect()->route('jenis-pelatihan.index')
                            ->with('success', 'Jenis Pelatihan berhasil dihapus');
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus jenis pelatihan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                            ->with('error', 'Gagal menghapus jenis pelatihan: ' . $e->getMessage());
        }
    }
}
