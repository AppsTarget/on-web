<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pastas extends Model
{
    protected $table = "pastas";
    protected $fillable = [
        'id',
        'nome',
        'dump',
        'created_at',
        'updated_at'
    ];
}
