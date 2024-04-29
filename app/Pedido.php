<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = "pedido";
    protected $fillable = [
        'id',
        'id_emp',
        'lixeira',
        'id_orcamento',
        'id_paciente',
        'id_convenio',
        'id_prof_exa',
        'id_agendamento',
        'num_pedido',
        'data',
        'consultor',
        'hora',
        'data_validade',
        'data_congelamento',
        'data_descongelar',
        'status',
        'obs',
        'total',
        'tipo_contrato',
        'created_by',
        'contrato',
        'id_encaminhamento'
    ];
}
