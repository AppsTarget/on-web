<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoStatus extends Model
{
    protected $table = "pedido_status";
    protected $fillable = [
        'id',
        'id_pedido',
        'status',
        'created_at',
        'updated_at'
    ];
}