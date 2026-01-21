<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiPelatihan extends Model
{
    use HasFactory;

    protected $table = 'presensi_pelatihans';
    protected $primaryKey = 'id_presensi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // 'id_presensi',
        'id_jadwal',
        'id_karyawan',
        'status_kehadiran',
        'waktu_presensi',
        'bukti_kehadiran',
        'dicatat_oleh',
        'user_latitude',
        'user_longitude'
    ];

    protected $casts = [
        'waktu_presensi' => 'datetime',
        'user_latitude' => 'double',
        'user_longitude' => 'double',
    ];

    //Relationships
    public function JadwalPelatihan()
    {
        return $this->belongsTo(JadwalPelatihan::class, 'id_jadwal', 'id_jadwal');
    }

    public function Karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function Pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh', 'email');
    }

    /**
     * Hitung jarak antara dua koordinat menggunakan Haversine Formula
     * @param float $userLat Latitude user
     * @param float $userLon Longitude user
     * @param float $locLat Latitude lokasi
     * @param float $locLon Longitude lokasi
     * @return float Jarak dalam meter
     */
    public static function calculateDistance($userLat, $userLon, $locLat, $locLon)
    {
        $earthRadiusM = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($locLat - $userLat);
        $dLon = deg2rad($locLon - $userLon);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($userLat)) * cos(deg2rad($locLat)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadiusM * $c;

        return $distance;
    }
}
