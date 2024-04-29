<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encaminhamento extends Model
{
    protected $table = 'enc2_encaminhamentos';
    protected $fillable = [
        'id',
        'id_de',
        'id_para',
        'id_paciente',
        'id_especialidade',
        'id_cid',
        'id_anexo',
        'descr',
        'data',
        'data_validade',
        'lixeira',
        'id_solicitacao'
    ];
}