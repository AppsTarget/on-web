<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encaminhamento_old extends Model
{
    protected $table = 'encaminhamento';
    protected $fillable = [
        'id',
        'id_emp',
        'id_agendamento',
        'id_profissional',
        'id_paciente',
        'id_evolucao',
        'sucess'
    ];
}