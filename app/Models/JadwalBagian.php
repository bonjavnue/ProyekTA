<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalBagian extends Model
{
    use HasFactory;
    protected $table = 'jadwal_bagian';
    protected $primaryKey = 'id_jadwalBagian';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // 'id_jadwalBagian',
        'id_jadwal',
        'id_bagian',
    ];

    //Relationships
    public function Bagian()
    {
        return $this->belongsTo(Bagian::class, 'id_bagian', 'id_bagian');
    }

    public function JadwalPelatihan()
    {
        return $this->belongsTo(JadwalPelatihan::class, 'id_jadwal', 'id_jadwal');
    }

}
