<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preco extends Model
{
    protected $table = "preco";
    protected $fillable = [
        'id',
        'id_emp',
        'id_tabela_preco',
        'id_procedimento',
        'valor',
        'valor_prazo',
        'valor_minimo'
    ];
}
