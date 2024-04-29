<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoFormaPag extends Model
{
    protected $table = "pedido_forma_pag";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido',
        'id_forma_pag',
        'id_financeira',
        'num_total_parcela',
        'valor_total',
        'tipo'
    ];
}
