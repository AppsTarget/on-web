<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoricoAgenda extends Model
{
    protected $table = "historico_agenda";
    protected $fillable = [
        'id',
        'id_emp',
        'id_agenda',
        'id_status',
        'id_tipo_procedimento',
        'id_procedimento',
        'id_tipo_confirmacao',
        'campo'
    ];
}