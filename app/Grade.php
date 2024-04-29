<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = "grade";
    protected $fillable = [
        'id',
        'id_profissional',
        'id_etiqueta',
        'dia_semana',
        'data_inicial',
        'data_final',
        'hora_inicial',
        'hora_final',
        'max_qtde_pacientes',
        'min_intervalo',
        'obs',
        'grade_divisao',
        'ativo',
        'lixeira'
    ];
}
