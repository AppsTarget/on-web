<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovConta extends Model
{
    protected $table = "mov_conta";
    protected $fillable = [
        'id', 
        'id_conta',
        'id_titulo',
        'tipo',
        'valor', 
        'saldo_anterior',
        'desconto',
        'acrescimo',
        'saldo_resultante',
        'created_by_descr'
    ];
}