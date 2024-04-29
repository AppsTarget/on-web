<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnamneseResposta extends Model
{
    protected $table = "anamnese_resposta";
    protected $fillable = [
        'id',
        'id_emp',
        'id_anamnese_pessoa',
        'id_pergunta',
        'resposta'
    ];
}
