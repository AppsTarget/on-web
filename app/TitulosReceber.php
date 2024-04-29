<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TitulosReceber extends Model
{
    protected $table = "titulos_receber";
    protected $fillable = [
        'id', 
        'descr', 
        'ndoc',
        'id_pedido',
        'id_conta',
        'id_financeira',
        'origem', 
        'parcela', 
        'id_forma_pag,', 
        'forma_pag,', 
        'id_pedido_forma_pag', 
        'id_pessoa',
        'id_historico',
        'd_entrada', 
        'h_entrada', 
        'd_emissao', 
        'h_emissao', 
        'd_vencimento', 
        'h_vencimento', 
        'id_forma_pag_pago',
        'd_pago', 
        'h_pago', 
        'pago',
        'pago_por',
        'pago_por_descr',
        'created_by',
        'created_by_descr',
        'updated_by',
        'updated_by_descr',
        'valor_total',
        'valor_total_pago',
        'movimento',
        'obs',
        'taxa_financeira',
        'lixeira',
        'id_sala'
    ];
}