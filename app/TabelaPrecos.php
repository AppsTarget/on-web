<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TabelaPrecos extends Model
{
    protected $table = "tabela_precos";
    protected $fillable = [
        'id',
        'id_empresa',
        'descr',
        'status',
        'valor',
        'vigencia',
        'max_atv_semana',
        'max_atv',
        'repor-som-mes',
        'desconto_associados',
        'desconto_geral',
        'pre_agendamento',
        'habilitacao',
        'reabilitacao',
        'contrato',
        'descr_contrato',
        'lixeira'
    ];

}
