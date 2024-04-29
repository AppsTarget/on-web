<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrcamentoServicos extends Model
{
    protected $table = "orcamento_servicos";
    protected $fillable = [
        'id',
        'id_emp',
        'id_orcamento',
        'id_tabela_preco',
        'id_prof_exe',
        'dente_regiao',
        'face',
        'qtde',
        'valor',
        'valor_prazo',
        'num_guia',
        'autorizado',
        'obs',
        'data_autorizado',
        'qtde_autorizado'
    ];
}
