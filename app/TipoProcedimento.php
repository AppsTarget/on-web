<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipoprocedimento extends Model
{
    protected $table = "tipo_procedimento";
    protected $fillable = [
        'id',
        'id_emp',
        'assossiar_especialidade',
        'id_especialidade',
        'assossiar_contrato',
        'descr',
        'tempo-procedimento',
        'lixeira'
    ];
}
