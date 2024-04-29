<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EncaminhantesEspecialidade extends Model
{
    protected $table = 'enc2_encaminhantes_especialidade';
    protected $fillable = [
        'id',
        'id_encaminhante',
        'id_especialidade'
    ];
}