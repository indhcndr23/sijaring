<?php

namespace App\Models;

class RoleGroup extends BaseModel
{
    protected $table            = 'role_group';
    protected $returnType       = \App\Entities\RoleGroup::class;
    protected $allowedFields    = [];
}
