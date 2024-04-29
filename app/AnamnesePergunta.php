<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnamnesePergunta extends Model
{
    protected $table = "anamnese_pergunta";
    protected $fillable = [
        'id',
        'id_emp',
        'id_anamnese',
        'tipo',
        'pergunta',
        'obs',
        'criacao',
    ];
}