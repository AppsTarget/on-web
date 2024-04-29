<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anexos extends Model
{
    protected $table = "anexos";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_profissional',
        'titulo',
        'obs',
        'pasta',
        'lixeira'
    ];
}
