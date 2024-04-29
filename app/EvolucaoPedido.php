<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvolucaoPedido extends Model
{
    protected $table = "evolucao_pedido";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido_servicos',
        'id_paciente',
        'id_profissional',
        'id_evolucao_tipo',
        'data',
        'hora',
        'cid',
        'estado',
        'titulo',
        'diagnostico'
    ];
}
