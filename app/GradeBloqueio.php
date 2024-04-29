<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeBloqueio extends Model
{
    protected $table = "grade_bloqueio";
    protected $fillable = [
        'id',
        'id_profissional',
        'data_inicial',
        'data_final',
        'hora_inicial',
        'hora_final',
        'dia_semana',
        'ativo'
    ];
}
