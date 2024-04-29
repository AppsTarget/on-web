<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpresasPlano extends Model
{
    protected $table = "empresas_plano";
    protected $fillable = [
        'id',
        'id_tabela_preco',
        'id_emp'
    ];
}