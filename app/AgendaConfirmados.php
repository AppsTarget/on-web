<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaConfirmados extends Model
{
    protected $table = "agenda_confirmados";
    protected $fillable = [
        'id',
        'id_agenda',
        'id_emp',
        'id_tipo_agendamento',
        'valor_total',
        'created_by'
    ];
}