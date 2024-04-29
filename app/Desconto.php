<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desconto extends Model
{
    protected $table = "desconto";
    protected $fillable = [
        "id",
        "id_supervisor",
        "id_pedido",
        "motivo"
    ];
}
