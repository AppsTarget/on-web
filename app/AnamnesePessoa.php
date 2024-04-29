<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnamnesePessoa extends Model
{
    protected $table = "anamnese_pessoa";
    protected $fillable = [
        'id',
        'id_emp',
        'id_anamnese',
        'id_membro',
        'id_pessoa',
        'publico',
        'data',
        'hora',
        'lixeira'
    ];
}