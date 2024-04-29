<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Congelamentos extends Model
{
    protected $table = "congelamentos";
    protected $fillable = [
        'id',
        'id_pedido',
        'data',
        'acao',
        'created_at',
        'updated_at'
    ];
}