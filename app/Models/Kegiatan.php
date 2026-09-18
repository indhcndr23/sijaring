<?php

namespace App\Models;

class Kegiatan extends BaseModel
{
    protected $table            = 'kegiatan';
    protected $returnType       = \App\Entities\Kegiatan::class;
    protected $allowedFields    = [];
}
