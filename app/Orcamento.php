<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $table = "orcamento";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_convenio',
        'id_prof_exa',
        'num_pedido',
        'data',
        'hora',
        'data_validade',
        'status',
        'obs',
        'total',
        'total_prazo',
        'total_aprovado',
        'id_forma_pag_vista',
        'valor_vista',
        'id_forma_pag_prazo',
        'id_financeira_prazo',
        'parcela_prazo',
        'valor_prazo'
    ];
}
