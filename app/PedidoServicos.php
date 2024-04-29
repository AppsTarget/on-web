<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoServicos extends Model
{
    protected $table = "pedido_servicos";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pedido',
        'id_orcamento',
        'id_tabela_preco',
        'id_prof_exe',
        'id_procedimento',
        'dente_regiao',
        'face',
        'qtde',
        'valor',
        'valor_prazo',
        'num_guia',
        'obs',
        'status',
        'id_prof_finalizado',
        'data_finalizado',
        'hora_finalizado'
    ];
}
