<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldMovAtividades extends Model
{
    protected $table = "old_mov_atividades";
    protected $fillable = [
        "id",
        "id_status",
        "id_atividade",
        "seq, id_membro",
        "id_grade",
        "id_pedido",
        "id_confirmacao",
        "data",
        "hora",
        "usu_criado",
        "dt_criado",
        "hr_criado",
        "status",
        "usu_confirm",
        "dt_confirm",
        "hr_confirm",
        "perc_comis",
        "guia_convenio",
        "finan_id",
        "id_tipo_procedimento",
        'obs_cancelamento',
        'motivo_cancelamento'
    ];
}
