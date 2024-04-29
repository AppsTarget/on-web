<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrcamentoFormaPag extends Model
{
    protected $table = "orcamento_forma_pag";
    protected $fillable = [
        'id',
        'id_emp',
        'id_orcamento',
        'id_forma_pag',
        'id_financeira',
        'num_parcela',
        'valor',
        'tipo'
    ];

}
