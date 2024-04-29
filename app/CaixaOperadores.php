<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaixaOperadores extends Model
{
    protected $table = "caixa_operadores";
    protected $fillable = [
        "id",
        "id_caixa",
        "id_operador"
    ];
}