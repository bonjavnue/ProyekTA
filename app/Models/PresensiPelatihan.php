<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiPelatihan extends Model
{
    use HasFactory;

    protected $table = 'presensi_pelatihan';
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
        'dicatat_oleh'
    ];

    protected $casts = [
        'waktu_presensi' => 'datetime',
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
}
