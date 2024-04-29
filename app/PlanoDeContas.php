<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanoDeContas extends Model
{
    protected $table = "plano_de_contas";
    protected $fillable = [
        'id',
        'id_pai',
        'id_emp',
        'descr',
        'lixeira',
    ];
}
