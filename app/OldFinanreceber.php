<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldFinanreceber extends Model
{
    protected $table = "old_finanreceber";
    protected $fillable = [
        "id",
        "id_planopagamento",
        "id_contrato",
        "id_caixa",
        "id_operador",
        "id_rede_adquirente",
        "parcela",
        "valor",
        "acres",
        "valorpago",
        "datavenc",
        "dtemis",
        "datalanc",
        "datapag",
        "horalanc",
        "usuario",
        "numcheque",
        "agencia",
        "conta",
        "seqcheque",
        "banco",
        "situacao",
        "id_emp",
        "id_financeira",
        "guia",
        "descr",
        "nsu_doc",
        "ndoc",
        "pessoa_id",
        "historico_id"
    ];
}
