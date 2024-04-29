<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Financeira extends Model
{
    protected $table = "financeira";
    protected $fillable = [
        'id',
        'id_emp',
        'tipo_de_baixa',
        'descr',
        'prazo',
        'taxa_padrao'
    ];
}
