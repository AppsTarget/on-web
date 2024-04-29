<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaStatus extends Model
{
    protected $table = "agenda_status";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'cor',
        'cor_letra',
        'permite_editar',
        'permite_fila_espera',
        'permite_reagendar',
        'caso_reagendar',
        'case_confirmar',
        'libera_horario',
        'lixeira'
    ];
}
