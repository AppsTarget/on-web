<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $table = "caixa";
    protected $fillable = [
        "id",
        "id_emp",
        "descr",
        "valor",
        'd_ult_abertura',
        "h_ult_abertura",
        "h_abertura",
        "h_fechamento",
        "situacao",
        "ativo",
        "lixeira"
    ];
}