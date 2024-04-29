<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DadosApp extends Model
{
    protected $table = "dados_app";
    protected $fillable = [
        'id',
        'id_paciente',
        'tipo_dados',
        'inicial',
        'final',
        'data',
        'valor'
    ];
}
