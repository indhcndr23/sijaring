<?php

namespace Modules\Dashboard\Models;

use CodeIgniter\Model;

class Opd extends Model
{
    protected $table         = 'opd';
    protected $primaryKey    = 'id_opd';
    protected $allowedFields = [
        'nama_opd',
        'alamat',
        'nama_pic',
        'no_hp_pic',
        'latitude',
        'longitude',
        'jumlah_pegawai',
        'jumlah_perangkat',
        'luas_ruangan',
        'rata_tamu',
        'jenis_dinding',
        'jumlah_lantai',
        'jumlah_ruangan',
        'jenis_kabel'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}