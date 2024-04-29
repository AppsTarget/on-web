<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovCredito extends Model
{
    protected $table = "mov_credito";
    protected $fillable = [
        'id',
        'id_pedido',
        'id_pessoa',
        'valor',
        'tipo_transacao',
        'planos',
        'saldo',
        'created_at',
        'updated_at',
    ];

}
