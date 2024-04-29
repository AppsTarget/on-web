<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncaminhamentoDetalhes extends Model
{
    protected $table = 'encaminhamento_detalhes';
    protected $fillable = [
        'id',
        'id_encaminhamento',
        'tipo',
        'valor1',
        'valor2',
        'valor3'
    ];
}