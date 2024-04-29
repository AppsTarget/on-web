<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpresasProfissional extends Model
{
    protected $table = "empresas_profissional";
    protected $fillable = [
        'id',
        'id_profissional',
        'id_emp'
    ];
}