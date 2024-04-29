<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinanceiraFormasPag extends Model
{
    protected $table = "financeira_formas_pag";
    protected $fillable = [
        'id',
        'id_emp',
        'id_forma_pag',
        'id_financeira'
    ];
}
