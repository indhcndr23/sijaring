<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class OpdModel extends Model
{
    protected $table         = 'opd';
    protected $primaryKey    = 'id_opd';
    protected $allowedFields = [
        'kelompok_opd_id',
        'nama_opd', 
        'jumlah_pegawai', 
        'jumlah_perangkat',
        'luas_ruangan',
        'rata_tamu',
        'jenis_dinding',
        'jumlah_lantai',
        'jumlah_ruangan',
        'jenis_kabel',
        'alamat', 
        'nama_pic', 
        'no_hp_pic', 
        'latitude', 
        'longitude'
    ];
    protected $returnType    = 'array';
}