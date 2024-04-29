<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldAtividades extends Model
{
    protected $table = "old_atividades";
    protected $fillable = [
        "id",
        "id_modalidade",
        "id_contrato",
        "id_cardapio_preco",
        "qtd_ini",
        "qtd",
        "periodo_dias",
        "id_emp",
        "valor_modalidade",
        "valor_cardapio",
        "convenia",
        "promocao",
        "qtd_semana"
    ];
}
