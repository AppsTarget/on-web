<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoParcela extends Model
{
    protected $table = "pedido_parcela";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido_forma_pag',
        'parcela',
        'valor',
        'vencimento'
    ];
}
