<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnamneseOpcao extends Model
{
    protected $table = "anamnese_opcao";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pergunta',
        'descr'
    ];
}
