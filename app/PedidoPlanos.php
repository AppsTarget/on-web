<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoPlanos extends Model
{
    protected $table = "pedido_planos";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido',
        'id_plano',
        'id_profissional',
        'qtde',
        'qtd_total',
        'data_validade',
        'data_congelamento',
        'data_descongelar',
        'valor',
        'valor_original',
        'descr'
    ];
}
