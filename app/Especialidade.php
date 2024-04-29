<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class especialidade extends Model
{
    protected $table = "especialidade";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'externo',
        'lixeira'
    ];
}
