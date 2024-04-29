<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilaEspera extends Model
{
    protected $table = "fila_espera";
    protected $fillable = [
        'id',
        'id_emp',
        'id_profissional',
        'id_paciente',
        'id_agendamento',
        'data_chegada',
        'hora_chegada',
        'status'
    ];
}
