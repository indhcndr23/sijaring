<?php

namespace App\Models;

class User extends BaseModel
{
    protected $table            = 'user';
    protected $returnType       = \App\Entities\User::class;
    protected $allowedFields    = [];
}
