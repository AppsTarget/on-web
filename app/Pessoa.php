<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $table = "pessoa";
    protected $fillable = [
        'id',
        'id_emp',
        'cod_interno',
        'nome_fantasia',
        'nome_reduzido',
        'profissao',
        'razao_social',
        'email',
        'tpessoa',
        'sexo',
        'estado_civil',
        'colaborador',
        'cliente',
        'paciente',
        'fornecedor',
        'administrador',
        'data_nasc',
        'peso',
        'altura',
        'cpf_cnpj',
        'rg_ie',
        'crm_cro',
        'num_convenio',
        'resp_nome',
        'resp_grau_parente',
        'resp_cpf',
        'resp_rg',
        'resp_celular',
        'resp_cep',
        'resp_endereco',
        'resp_numero',
        'resp_complemento',
        'resp_bairro',
        'resp_cidade',
        'resp_uf',
        'celular1',
        'celular2',
        'telefone1',
        'telefone2',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'banco',
        'tpconta',
        'agencia',
        'conta',
        'variacao',
        'obs',
        'creditos',
        'lixeira',
        'data_lixeira',
        'crm',
        'uf_crm',
        'cref',
        'uf_cref',
        'creft',
        'uf_creft',
        'crn',
        'uf-crn',
        'gera_faturamento',
        'd_naofaturar',
        'aplicar_desconto',
        'psq'
    ];
}
