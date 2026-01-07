<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;

    protected $table = 'bagian';
    protected $primaryKey = 'id_bagian';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_bagian',
        'nama_bagian',
        'email',
    ];

    //Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
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
