<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salas extends Model
{
    protected $table = "salas";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'valor',
        'lixeira',
        'created_at',
        'updated_at'
    ];
}