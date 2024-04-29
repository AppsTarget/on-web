<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receita extends Model
{
    protected $table = "receita";
    protected $fillable = [
        'id',
        'id_emp',
        'id_profissional',
        'id_paciente'
    ];
}
