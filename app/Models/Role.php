<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $table            = 'role';
    protected $returnType       = \App\Entities\Role::class;
    protected $allowedFields    = [];
}
