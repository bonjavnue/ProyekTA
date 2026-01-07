<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelatihan extends Model
{
    use HasFactory;
    protected $table = 'jadwal_pelatihans';
    protected $primariKey = 'id_jadwal';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // 'id_jadwal',
        'id_jenis',
        'tanggal_pelaksanaan',
        'jam_mulai',
        'jam_selesai',
        'tempat',
        'tenggat_presensi',
        'link_presensi',
        'qr_code',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'tenggat_presensi' => 'datetime',
    ];

    //Relationships
    public function JenisPelatihan()
    {
        return $this->belongsTo(JenisPelatihan::class, 'id_jenis', 'id_jenis');
    }

    public function JadwalBagian()
    {
        return $this->hasMany(JadwalBagian::class, 'id_jadwal', 'id_jadwal');
    }

    public function presensiPelatihan()
    {
        return $this->hasMany(PresensiPelatihan::class, 'id_jadwal', 'id_jadwal');
    }
}
