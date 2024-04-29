<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{
    protected $table = "parametros";
    protected $fillable = [
        'id',
        'id_emp',
        'desconto_geral',
    ];
}