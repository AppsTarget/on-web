<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CID extends Model
{
    protected $table = "cid";
    protected $fillable = [
        'id',
        'codigo',
        'nome'
    ];
}
