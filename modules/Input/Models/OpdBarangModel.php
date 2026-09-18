<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class OpdBarangModel extends Model
{
    protected $table         = 'opd_barang';
    protected $primaryKey    = 'id_opd_barang';
    
    protected $allowedFields = [
        'id_opd', 
        'id_barang', 
        'jumlah', 
        'jumlah_port', 
        'merk', 
        'tahun', 
        'kondisi', 
        'keterangan', 
        'foto_bukti'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';
}