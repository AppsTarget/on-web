<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaixaMov extends Model
{
    protected $table = "caixa_mov";
    protected $fillable = [
        "id",
        "id_caixa",
        "descr",
        "id_forma_pag",
        "valor",
        "tipo",
        "data",
        "hora",
        "created_by",
        "created_by_descr",
        "saldo_anterior",
        "saldo_resultante"
    ];
}