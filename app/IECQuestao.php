<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IECQuestao extends Model
{
    protected $table = "IEC_questao";
    protected $fillable = [
        'id',
        'id_questionario',
        'id_pergunta',
        'obs',
        'pessimo',
        'ruim',
        'bom',
        'excelente'
    ];
}