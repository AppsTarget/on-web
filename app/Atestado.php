<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Atestado extends Model
{
    protected $table = "atestado";
    protected $fillable = [
        'id',
        'id_emp',
        'id_profissional',
        'id_paciente',
        'CID',
        'data',
        'periodo'
    ];
}