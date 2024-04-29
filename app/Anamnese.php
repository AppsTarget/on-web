<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anamnese extends Model
{
    protected $table = "anamnese";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'ativo'
    ];
}