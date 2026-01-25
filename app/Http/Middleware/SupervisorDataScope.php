<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Bagian;
use App\Models\JadwalPelatihan;

class SupervisorDataScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply restrictions to supervisors
        if (auth()->user()->role !== 'supervisor') {
            return $next($request);
        }

        // Get supervisor's bagian
        $supervisorBagian = Bagian::where('email', auth()->user()->email)->first();

        if (!$supervisorBagian) {
            // Supervisor tanpa bagian tidak bisa akses kehadiran detail
            if ($request->routeIs('kehadiran.show')) {
                abort(403, 'Supervisor ini tidak memiliki bagian yang terikat');
            }
            return $next($request);
        }

        // Validate if trying to access a specific jadwal in kehadiran detail
        if ($request->routeIs('kehadiran.show')) {
            $jadwalId = $request->route('id');
            $jadwal = JadwalPelatihan::findOrFail($jadwalId);

            // Check if this jadwal involves the supervisor's bagian
            $jadwalBagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();

            if (!in_array($supervisorBagian->id_bagian, $jadwalBagianIds)) {
                abort(403, 'Anda tidak memiliki akses ke jadwal ini');
            }
        }

        return $next($request);
    }
}
