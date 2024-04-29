<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IECPessoa extends Model
{
    protected $table = "IEC_pessoa";
    protected $fillable = [
        'id',
        'id_questionario',
        'id_membro',
        'id_paciente',
        'destacar',
        'obs',
        'lixeira',
        'id_emp'
    ];
}