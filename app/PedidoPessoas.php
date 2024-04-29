<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoPessoas extends Model
{
    protected $table = "pedido_pessoas";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido_plano',
        'id_pessoa'
    ];
}
