<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContratoDados extends Model
{
    protected $table = "contrato_dados";
    protected $fillable = [
        "id",
        "id_contrato",
        "variable",
        "value"
    ];
}
