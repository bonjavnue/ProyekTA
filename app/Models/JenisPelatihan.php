<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPelatihan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pelatihan';
    protected $primaryKey = 'id_jenis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jenis',
        'nama_jenis',
        'deskripsi',
    ];

    //Relationships
    public function JadwalPelatihan()
    {
        return $this->hasMany(JadwalPelatihan::class, 'id_jenis', 'id_jenis');
    }
    
    // Query Scopes untuk pencarian dan filtering
    public function scopeSearch($query, $search)
    {
        return $query->where('nama_jenis', 'like', '%' . $search . '%')
                     ->orWhere('deskripsi', 'like', '%' . $search . '%')
                     ->orWhere('id_jenis', $search);
    }

    public function scopeOrderByName($query)
    {
        return $query->orderBy('nama_jenis', 'asc');
    }

    public function scopeOrderByCreated($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

