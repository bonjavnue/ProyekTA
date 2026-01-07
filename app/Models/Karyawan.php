<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_karyawan',
        'nama_karyawan',
        'id_bagian',
        'status_karyawan',
        'no_telepon',
    ];

    //Relationship

    public function Bagian()
    {
        return $this->belongsTo(Bagian::class, 'id_bagian', 'id_bagian');
    }
    
    public function PresensiPelatihan()
    {
        return $this->hasMany(PresensiPelatihan::class, 'id_karyawan', 'id_karyawan');
    }

}
