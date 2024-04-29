<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modalidades_por_plano extends Model
{
    protected $table = "modalidades_por_plano";
    protected $fillable = [
        'id',
        'id_empresa',
        'id_tabela_preco',
        'id_procedimento',
        'created_at',
        'updates_at'
    ];

}
