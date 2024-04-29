<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncaminhamentoSolicitacao extends Model
{
    protected $table = 'enc2_solicitacao';
    protected $fillable = [
        'id',
        'id_de',
        'id_para',
        'id_cid',
        'id_especialidade',
        'id_paciente',
        'lixeira',
        'id_procedimento',
        'atv_semana',
        'retorno',
        'obs',
        'id_emp'
    ];
}