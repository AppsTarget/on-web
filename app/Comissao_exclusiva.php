<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comissao_exclusiva extends Model
{
    protected $table = "comissao_exclusiva";
    protected $fillable = [
        'id',
        'id_empresa',
        'id_procedimento',
        'de2',
        'ate2',
        'valor2',
        'created_at',
        'updated_at'
    ];
}