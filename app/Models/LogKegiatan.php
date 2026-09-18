<?php

namespace App\Models;

class LogKegiatan extends BaseModel
{
    protected $table            = 'log_kegiatan';
    protected $returnType       = \App\Entities\LogKegiatan::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
}
