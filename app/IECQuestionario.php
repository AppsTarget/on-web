<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IECQuestionario extends Model
{
    protected $table = "IEC_questionario";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'ativo',
        'lixeira'
    ];
}