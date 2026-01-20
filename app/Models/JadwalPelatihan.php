<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelatihan extends Model
{
    use HasFactory;
    protected $table = 'jadwal_pelatihans';
    protected $primaryKey = 'id_jadwal';
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
        'waktu_mulai_presensi',
        'waktu_berakhir_presensi',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'tenggat_presensi' => 'datetime',
        'waktu_mulai_presensi' => 'datetime',
        'waktu_berakhir_presensi' => 'datetime',
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

    public function PresensiPelatihan()
    {
        return $this->hasMany(PresensiPelatihan::class, 'id_jadwal', 'id_jadwal');
    }


    public function getStatusAttribute($value)
    {
        // Jika sudah lewat H+5 → SELESAI (permanent)
        if (now() > $this->tenggat_presensi) {
            if ($value !== 'selesai') {
                $this->update(['status' => 'selesai']);
            }
            return 'selesai';
        }

        // Jika ada link presensi yang masih aktif → PUBLISHED
        if ($this->waktu_berakhir_presensi && now() <= $this->waktu_berakhir_presensi) {
            return 'published';
        }

        // Default → DRAFT
        return 'draft';
    }

    // Check apakah bisa generate presensi
    public function canGeneratePresensi()
    {
        // Hanya bisa generate jika:
        // 1. Belum lewat H+5
        // 2. Jam pelatihan sudah mulai
        // 3. Status bukan 'selesai'
        return now() <= $this->tenggat_presensi 
            && now() >= $this->jam_mulai 
            && $this->status !== 'selesai';
    }    

}
