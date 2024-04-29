<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormaPag extends Model
{
    protected $table = "forma_pag";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'max_parcelas',
        'dias_entre_parcela',
        'avista_prazo'
    ];
}
