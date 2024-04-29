<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = "medicamento";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'uso',
        'tipo',
        'unidade',
        'posologia',
        'ativo'
    ];
}