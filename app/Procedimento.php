<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class procedimento extends Model
{
    protected $table = "procedimento";
    protected $fillable = [
        'id',
        'id_emp',
        'id_especialidade',
        'id_comissao_exclusiva',
        'cod_tuss',
        'tempo_procedimento',
        'descr',
        'descr_resumida',
        'obs',
        'dente_regiao',
        'face',
        'faturar',
        'oculto',
        'valor_total',
        'total_agendamentos_meta',
        'tipo_de_comissao'
    ];
}
