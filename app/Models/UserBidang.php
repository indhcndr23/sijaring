<?php

namespace App\Models;

class UserBidang extends BaseModel
{
    protected $table            = 'user_bidang';
    protected $returnType       = \App\Entities\UserBidang::class;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
}
