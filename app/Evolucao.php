<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evolucao extends Model
{
    protected $table = "evolucao";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_profissional',
        'id_evolucao_tipo',
        'id_corpo',
        'id_area',
        'data',
        'hora',
        'cid',
        'estado',
        'titulo',
        'diagnostico',
        'lixeira'
    ];
}