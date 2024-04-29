<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $table = "convenio";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pessoa',
        'quem_paga',
        'descr',
        'prazo',
        'lixeira'
    ];
}
