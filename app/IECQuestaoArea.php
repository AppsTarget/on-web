<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IECQuestaoArea extends Model
{
    protected $table = "IEC_questao_area";
    protected $fillable = [
        'id',
        'id_questao',
        'id_area',
        'status',
    ];
}