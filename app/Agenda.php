<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = "agenda";
    protected $fillable = [
        "id",
        "id_emp",
        "id_profissional",
        "id_paciente",
        "id_tipo_procedimento",
        "id_grade_horario",
        "id_convenio",
        "id_status",
        "id_confirmacao",
        "id_reagendado",
        "id_pedido",
        "id_tabela_preco",
        "id_modalidade",
        "data",
        "hora",
        "dia_semana",
        "obs",
        "status",
        "motivo_cancelamento",
        "obs_cancelamento",
        "reagendamento",
        "bordero",
        "lixeira",
        "notificado",
        "id_encaminhamento",
        "travar"
    ];
}
