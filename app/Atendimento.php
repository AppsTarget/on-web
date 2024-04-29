<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    protected $table = "atendimento";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_agendamento',
        'data',
        'hora_inicio',
        'hora_fim',
        'created_by',
        'updated_by'
    ];
}
