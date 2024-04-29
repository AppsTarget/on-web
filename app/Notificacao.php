<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = "notificacao";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_profissional',
        'assunto',
        'notificacao',
        'publico',
        'lixeira',
        'created_by'
    ];
}
