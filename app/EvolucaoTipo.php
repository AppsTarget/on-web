<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvolucaoTipo extends Model
{
    protected $table = "evolucao_tipo";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'prioritario'
    ];
}
