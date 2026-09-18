<?php

namespace Modules\KalenderKegiatan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kegiatan';

    public function user(): object
    {
        return $this->belongsTo(User::class);
    }
}
