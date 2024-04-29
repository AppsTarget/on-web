<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TabelaPrecosVigencia extends Model
{
    protected $table = "tabela_precos_vigencia";
    protected $fillable = [
        'id',
        'id_tabela_preco',
        'de',
        'ate',
        'vigencia',
    ];

}
