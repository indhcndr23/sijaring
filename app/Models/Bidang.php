<?php

namespace App\Models;

class Bidang extends BaseModel
{
    protected $table            = 'bidang';
    protected $returnType       = \App\Entities\Bidang::class;
    protected $allowedFields    = [];
}
