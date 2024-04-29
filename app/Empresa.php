<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {
    protected $table = "empresa";
    protected $fillable = [
        'id',
        'descr',
        'cidade',
        'uf',
        'endereco',
        'tipo',
        'telefone',
        'dump',
        'mod_agenda_semanal',
        'mod_fila_espera',
        'mod_tempo_consulta',
        'mod_planos_tratamento',
        'mod_financeiro',
        'mod_mostrar_foto',
        'mod_cod_interno',
        'mod_trava_foto_paciente',
        'mod_impressao_especifica'
    ];
}