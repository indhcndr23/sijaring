<?php

namespace Modules\Input\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table         = 'barang';
    protected $primaryKey    = 'id_barang';
    protected $allowedFields = ['jenis_barang']; // Cukup jenis_barang saja
    protected $useTimestamps = true;
    protected $returnType    = 'array';
}