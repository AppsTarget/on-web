<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoLog extends Model
{
    protected $table = "pedido_log";
    protected $fillable = [
        'id',
        'id_pedido',
        'descr',
        'data',
        'hora'
    ];
}