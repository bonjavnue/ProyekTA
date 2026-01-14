<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Bagian;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource with search, sorting and pagination.
     */
    public function index()
    {
        $search = request('search', '');
        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $perPage = request('per_page', 10);

        // Validasi per_page - hanya terima nilai yang diizinkan
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Validasi sort_by - hanya terima kolom yang valid
        $allowedSortBy = ['id_karyawan', 'nik', 'nama_karyawan', 'id_bagian', 'status_karyawan', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validasi sort_order
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $query = Karyawan::with('Bagian');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('status_karyawan', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhereHas('Bagian', function($q) use ($search) {
                      $q->where('nama_bagian', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting dengan parameter dinamis
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results with dynamic per_page
        $karyawans = $query->paginate($perPage)->appends(request()->query());
        $bagians = Bagian::all();

        return view('admin.datakaryawan', compact('karyawans', 'bagians', 'search', 'sortBy', 'sortOrder', 'perPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_karyawan' => 'required|integer|unique:karyawans,id_karyawan',
                'nik' => 'required|string|unique:karyawans,nik|max:20',
                'nama_karyawan' => 'required|string|max:255',
                'id_bagian' => 'required|exists:bagian,id_bagian',
                'status_karyawan' => 'required|in:Tetap,Kontrak,Cuti,Tidak Aktif',
                'no_telepon' => 'nullable|string|max:20',
            ]);

            // Simpan ke database dengan ID yang diisi user
            $karyawan = Karyawan::create($validated);

            // Jika AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Karyawan berhasil ditambahkan',
                    'data' => $karyawan
                ], 201);
            }

            return redirect()->route('karyawan.index')
                            ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                            ->with('error', 'Gagal menambahkan karyawan')
                            ->withErrors($e->errors())
                            ->withInput();
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);

            $validated = $request->validate([
                'nik' => 'required|string|unique:karyawans,nik,' . $id . ',id_karyawan|max:20',
                'nama_karyawan' => 'required|string|max:255',
                'id_bagian' => 'required|exists:bagian,id_bagian',
                'status_karyawan' => 'required|in:Tetap,Kontrak,Cuti,Tidak Aktif',
                'no_telepon' => 'nullable|string|max:20',
            ]);

            // Update data
            $karyawan->update($validated);

            // Jika AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Karyawan berhasil diperbarui',
                    'data' => $karyawan
                ], 200);
            }

            return redirect()->route('karyawan.index')
                            ->with('success', 'Karyawan berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                            ->with('error', 'Gagal memperbarui karyawan')
                            ->withErrors($e->errors())
                            ->withInput();
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui karyawan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'Gagal memperbarui karyawan: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);

            // Check if karyawan has related data (contoh: jadwal pelatihan, absensi, dll)
            // Uncomment jika ada relasi yang perlu dicek
            /*
            if ($karyawan->JadwalPelatihan()->exists()) {
                $message = 'Tidak dapat menghapus karyawan ini karena masih terdapat jadwal pelatihan yang terhubung.';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return redirect()->route('karyawan.index')
                                ->with('error', $message);
            }
            */

            $karyawan->delete();

            // Jika AJAX request, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Karyawan berhasil dihapus'
                ], 200);
            }

            return redirect()->route('karyawan.index')
                            ->with('success', 'Karyawan berhasil dihapus');
        } catch (\Exception $e) {
            // Jika AJAX request, return JSON error
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                            ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    /**
     * Import karyawan dari Excel file
     */
    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
            ]);

            $file = $request->file('excel_file');
            $path = $file->store('temp');
            
            // Load file menggunakan SimpleXLSX atau CSV reader
            $filePath = storage_path('app/' . $path);
            $data = [];
            
            if ($file->getClientOriginalExtension() === 'csv') {
                // Parse CSV
                if (($handle = fopen($filePath, 'r')) !== false) {
                    $header = fgetcsv($handle);
                    while (($row = fgetcsv($handle)) !== false) {
                        if (!empty(array_filter($row))) {
                            $data[] = array_combine($header, $row);
                        }
                    }
                    fclose($handle);
                }
            } else {
                // Parse Excel menggunakan built-in PHP functions
                // Untuk Excel, gunakan library atau custom parser
                // Untuk sekarang, return error jika tidak CSV
                return response()->json([
                    'success' => false,
                    'message' => 'Saat ini hanya CSV yang didukung. Silakan convert Excel ke CSV terlebih dahulu.'
                ], 422);
            }

            // Validate dan import data
            $imported = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                try {
                    $validated = [
                        'id_karyawan' => intval($row['id_karyawan'] ?? null),
                        'nik' => $row['nik'] ?? null,
                        'nama_karyawan' => $row['nama_karyawan'] ?? null,
                        'id_bagian' => intval($row['id_bagian'] ?? null),
                        'status_karyawan' => $row['status_karyawan'] ?? 'Tetap',
                        'no_telepon' => $row['no_telepon'] ?? null,
                    ];

                    // Quick validation
                    if (empty($validated['id_karyawan']) || empty($validated['nik']) || empty($validated['nama_karyawan'])) {
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                        continue;
                    }

                    // Check if already exists
                    if (Karyawan::where('id_karyawan', $validated['id_karyawan'])->exists()) {
                        $errors[] = "Baris " . ($index + 2) . ": ID Karyawan {$validated['id_karyawan']} sudah ada";
                        continue;
                    }

                    if (Karyawan::where('nik', $validated['nik'])->exists()) {
                        $errors[] = "Baris " . ($index + 2) . ": NIK {$validated['nik']} sudah ada";
                        continue;
                    }

                    Karyawan::create($validated);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Delete temp file
            \Storage::delete($path);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$imported} karyawan berhasil diimpor",
                    'imported' => $imported,
                    'errors' => $errors,
                    'error_count' => count($errors)
                ]);
            }

            return redirect()->route('karyawan.index')
                            ->with('success', "{$imported} karyawan berhasil diimpor");
        } catch (\Exception $e) {
            // Delete temp file if exists
            if (isset($path)) {
                \Storage::delete($path);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }
}