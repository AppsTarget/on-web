<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceitaMedicamento extends Model
{
    protected $table = "receita_medicamento";
    protected $fillable = [
        'id',
        'id_emp',
        'id_receita',
        'id_medicamento',
        'descr_medicamento',
        'posologia'
    ];
}