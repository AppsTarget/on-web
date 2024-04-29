<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContasBancarias extends Model
{
    protected $table = "contas_bancarias";
    protected $fillable = [
        'id',
        'id_caixa',
        'titular',
        'numero',
        'agenda',
        'id_banco',
        'id_emp',
        'corrente',
        'aplicação',
        'caixa',
        'lixeira'
    ];
}