<?php

namespace App\Models;

class Opd extends BaseModel
{
    protected $table            = 'opd';
    protected $returnType       = \App\Entities\Opd::class;
    protected $allowedFields    = [];
}
