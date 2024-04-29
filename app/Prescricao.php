<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prescricao extends Model
{
    protected $table = "prescricao";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_profissional',
        'data',
        'corpo'
    ];
}
