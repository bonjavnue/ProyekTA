<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawans';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false; // ID tidak auto-increment
    protected $keyType = 'int'; // Tetap integer tapi tidak auto-increment

    protected $fillable = [
        'id_karyawan',
        'nik',
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

    // Query Scopes untuk pencarian dan filtering
    public function scopeSearch($query, $search)
    {
        return $query->where('id_karyawan', 'like', '%' . $search . '%')
                     ->orWhere('nama_karyawan', 'like', '%' . $search . '%')
                     ->orWhere('nik', 'like', '%' . $search . '%')
                     ->orWhereHas('Bagian', function ($q) use ($search) {
                         $q->where('nama_bagian', 'like', '%' . $search . '%');
                     });
    }

    public function scopeOrderByColumn($query, $column, $direction)
    {
        if (in_array($column, ['id_karyawan', 'nik', 'nama_karyawan', 'status_karyawan', 'created_at'])) {
            return $query->orderBy($column, $direction);
        } elseif ($column === 'nama_bagian') {
            return $query->join('bagian', 'karyawans.id_bagian', '=', 'bagian.id_bagian')
                         ->orderBy('bagian.nama_bagian', $direction)
                         ->select('karyawans.*');
        }
        return $query;
    }
}
