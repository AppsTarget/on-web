<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class especialidadePessoa extends Model
{
    protected $table = "especialidade_pessoa";
    protected $fillable = [
        'id',
        'id_profissional',
        'id_especialidade'
    ];
}