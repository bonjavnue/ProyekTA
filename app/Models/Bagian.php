<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;

    protected $table = 'bagian';
    protected $primaryKey = 'id_bagian';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_bagian',
        'email',
    ];

    //Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_bagian', 'bagian_id', 'user_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class,'id_bagian', 'id_bagian');
    }

    public function JadwalBagian()
    {
        return $this->hasMany(JadwalBagian::class, 'id_bagian', 'id_bagian');
    }

}
