<?php

namespace App\Models;

class KegiatanKategori extends BaseModel
{
    protected $table            = 'kegiatan_kategori';
    protected $returnType       = \App\Entities\KegiatanKategori::class;
    protected $allowedFields    = [];
}
