<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = "contrato";
    protected $fillable = [
        "id",
        "id_emp",
        "open_id",
        "id_pedido",
        "token",
        "status",
        "name",
        "original_file",
        "signed_file",
        "updated_at_ext",
        "created_by",
        "updated_at",
        "created_by_descr"
    ];
}
