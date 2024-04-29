<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrecoConvenioplanos extends Model
{
    protected $table = "preco_convenios_plano";
    protected $fillable = [
        'id',
        'id_emp',
        'id_convenio',
        'id_tabela_preco',
        'valor'
    ];
}
