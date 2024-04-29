<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssociadosRegra extends Model
{
    protected $table = "associados_regra";
    protected $fillable = [
        'id',
        'id_emp',
        'ativo',
        'lixeira',
        'dias_pos_fim_contrato'
    ];
}
