<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\Pedido;
use App\Pessoa;
use App\TabelaPrecos;
use App\Modalidades_por_plano;
use App\Procedimento;
use App\Comissao_exclusiva;
use App\OldModalidades;
use Illuminate\Http\Request;

class AssociadosPorPeriodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view("associados_por_periodo", compact('empresas'));
    }
    public function imprimirassociados($id_emp, $id_membro, $dinicial, $dfinal, $filtro, $orientacao){
        $datainicial = new \Datetime($dinicial);
        $datafinal = new \DateTime($dfinal);

        

        $data_inicial = $datainicial->format('d/m/Y');
        $data_final = $datafinal->format('d/m/Y');

        $consultor = '0';

        $contratos = DB::table('pedido')
                     ->select('pedido.data As data_inicial',
                              DB::raw("pedido.data_validade as data_final"),
                              DB::raw('pedido.id As id_contrato'),
                              'pessoa.nome_fantasia as associado',
                              DB::raw("GROUP_CONCAT(tabela_precos.descr) AS plano"),
                              'pedido.total AS valor',
                              'pedido.total AS total', DB::raw("(select 0) AS antigo"))
                     ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                     ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                     ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                     ->leftjoin('agenda', 'agenda.id', 'pedido.id')
                     ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                     ->where('pedido.lixeira', 0)
                     ->where('pedido.data', '>=',$datainicial->format('Y-m-d'))
                     ->where('pedido.data', '<=',$datafinal->format('Y-m-d'))
                     ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101) or pedido_forma_pag.id_forma_pag is null)')
                     ->where(function($sql) use ($id_membro) {
                         if ($id_membro <> 0){
                            $sql->where('id_prof_exa', $id_membro);
                         }
                     })
                     ->where('pedido.status', 'F')
                     ->where(function($sql) use ($filtro) {
                        if ($filtro == 'C'){
                            $sql->whereRaw("pedido.id_convenio <> 0");
                        }
                        if($filtro == 'P'){
                            $sql->whereRaw("pedido.id_convenio is NULL or
                                            pedido.id_convenio = 0");
                        }
                     })
                     ->where('pedido.id_emp', $id_emp)
                     ->orderBy('pedido.data')
                     ->groupBy('pedido.id',
                               'pedido.data',
                               'pedido.data_validade',
                               'pessoa.nome_fantasia',
                               'pedido.total')
                     ->unionAll(DB::table('old_faturamento_view')
                                ->select(DB::raw("old_faturamento_view.datainicial AS data_inicial"),
                                        DB::raw("old_contratos.datafinal           AS data_final"),
                                        DB::raw("old_faturamento_view.id_contrato  AS id_contrato"),
                                        DB::raw("old_faturamento_view.pessoa_nome  AS associado"),
                                        DB::raw("old_faturamento_view.modalidade   AS plano"),
                                        DB::raw("old_faturamento_view.valor        AS valor"),
                                        DB::raw("old_faturamento_view.total        AS total"), DB::raw("(select 1) AS antigo"))
                                ->leftjoin("old_contratos", "old_contratos.id", "old_faturamento_view.id_contrato")
                                ->leftjoin("old_finanreceber", "old_finanreceber.id_contrato", "old_contratos.id")
                                ->leftjoin("old_financeira", "old_financeira.id", "old_finanreceber.id_financeira")
                                ->leftjoin("old_mov_atividades", "old_mov_atividades.id", "old_contratos.id_agendamento")
                                ->where('old_faturamento_view.datainicial', '>=',$datainicial->format('Y-m-d'))
                                ->where('old_faturamento_view.datainicial', '<=',$datafinal->format('Y-m-d'))
                                ->where('old_contratos.id_emp', $id_emp)
                                ->where(function($sql) use($id_membro){
                                    if ($id_membro != 0 && $id_membro != '0'){
                                        $sql->where('old_contratos.consultor_id', $id_membro);
                                    }
                                })
                                ->where(function($sql) use ($filtro) {
                                    if ($filtro == 'C'){
                                        $sql->where('old_financeira.convenio', 'S');
                                    }
                                    if($filtro == 'P'){
                                        $sql->where('old_financeira.convenio', '<>', 'S');
                                    }
                                 })
                                 ->orderBy('old_faturamento_view.data')
                                //  ->where('old_faturamento_view.id_contrato', 68746)
                                ->groupBy("old_faturamento_view.datainicial",
                                          "old_contratos.datafinal",
                                          "old_faturamento_view.id_contrato",
                                          "old_faturamento_view.pessoa_nome",
                                          "old_faturamento_view.modalidade",
                                          "old_faturamento_view.valor",
                                          "old_faturamento_view.total")
                                )
                     ->orderBy('data_inicial')
                     ->get();
                                 
            // return $contratos;




            if ($id_membro != 0 && $id_membro != '0') {
                $consultor = DB::table('pessoa')
                             ->where('id', $id_membro)
                             ->value('nome_fantasia');

            }
                
        if ($orientacao == 'P'){
            return view('.reports.impresso_contratos_por_periodo', compact('data_inicial',
                                                                           'data_final', 
                                                                           'contratos',
                                                                            'consultor'));
        }
        else {
            return view('.reports.impresso_contratos_por_periodo2', compact('data_inicial',
                                                                           'data_final', 
                                                                           'contratos',
                                                                           'consultor'));
        }
    }

    public function imprimir($idempresa, $dinicial, $dfinal, $filtro, $inputqtd){

        $filtroi = new \DateTime($dinicial);

        if ($filtro == 'mes') {
            $filtroi = new \DateTime(date('Y-m', strtotime($dinicial)) . '-01');
            $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));
            $data_final1 = " and pedido_principal.data_validade <= '". $filtrof->format('Y-m-d') ."'";
            $data_final2 = " and datafinal <=  '". $filtrof->format('Y-m-d') ."'";
            $filtro = '';
        }
        else {
            $data_final1 = '';
            $data_final2 = '';
            $filtro = '';
        }
        

        

        if ($filtro == 'Qtd'){
            $filtro = " AND tab_aux.atv_consu <= ". $inputqtd;
        }
        if ($filtro == 'T'){
            $filtro = "";  
        }
        // if ($dfinal != 0){
        //     $filtrof = new \DateTime($dfinal);
        //     $data_final1 = "and pedido_principal.data_validade <= '" . $filtrof->format('Y-m-d') ."'";
        //     $data_final2 = " and datafinal <= '" . $filtrof->format('Y-m-d') ."'";
        // }
        else $filtrof = '';
        
        $ativos = DB::select(
            DB::raw(
                "Select 
                    id,
                    nome, 
                    GROUP_CONCAT(plano) AS plano, 
                    celular, 
                    MAX(data_validade) AS data_validade,
                    GROUP_CONCAT(vigencia) AS vigencia,
                    MAX(antigo) AS antigo,
                    SUM(max_qtde) AS max_qtde,
                    SUM(atv_consu) AS atv_consu,
                    MAX(forma_pag) AS forma_pag,
                    MAX(datainicial) as datainicial
                from 
                    (
                    select 
                        pessoa.nome_fantasia AS nome, 
                        (
                            select
                                p2.id
                            from
                                pedido AS p2
                            where
                                p2.data_validade = MAX(pedido_principal.data_validade) AND
                                p2.id_paciente = pessoa.id
                            LIMIT 1
                        ) AS id,
                        (
                            select
                                GROUP_CONCAT(tabela_precos.descr)
                            from
                                pedido_planos
                                left join pedido as p2 on p2.id = pedido_planos.id_pedido
                                left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
                            where
                                p2.data_validade = MAX(pedido_principal.data_validade) AND
                                p2.id_paciente = pessoa.id
                            group by
                                pedido_planos.id_pedido
                            LIMIT 1
                        ) as plano, 
                        pessoa.celular1 AS celular, 
                        MAX(pedido_principal.data_validade) AS data_validade,
                        (
                            select 
                                tabela_precos.vigencia 
                            from 
                                pedido_planos
                                left join pedido as p2 on p2.id = pedido_planos.id_pedido
                                left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
                            where
                                p2.data_validade = MAX(pedido_principal.data_validade) AND
                                p2.id_paciente   = pessoa.id
                            group by
                                pedido_planos.id_pedido,
                                tabela_precos.vigencia
                            LIMIT 1
                        ) AS vigencia,
                        (
                            select 
                                SUM(pedido_planos.qtde * t2.max_atv)
                            from
                                pedido_planos
                                left join pedido as p2 on p2.id = pedido_planos.id_pedido
                                left join tabela_precos as t2 on t2.id = pedido_planos.id_plano
                            where
                                p2.data_validade = MAX(pedido_principal.data_validade) AND
                                p2.id_paciente = pessoa.id
                            group by
                                p2.id
                            LIMIT 1
                        ) AS max_qtde,
                        (
                            (
                                select 
                                    SUM(pedido_planos.qtde * t2.max_atv)
                                from
                                    pedido_planos
                                    left join pedido as p2 on p2.id = pedido_planos.id_pedido
                                    left join tabela_precos as t2 on t2.id = pedido_planos.id_plano
                                where
                                    p2.data_validade = MAX(pedido_principal.data_validade) AND
                                    p2.id_paciente = pessoa.id
                                group by
                                    p2.id
                                LIMIT 1
                            )
                            -
                            COUNT(agenda.id)
                        ) AS atv_consu,
                        MAX(forma_pag.descr) AS forma_pag,
                        (
                            select 
                                p2.data
                            from
                                pedido_planos
                                left join pedido as p2 on p2.id = pedido_planos.id_pedido
                                left join tabela_precos as t2 on t2.id = pedido_planos.id_plano
                            where
                                p2.data_validade = MAX(pedido_principal.data_validade) AND
                                p2.id_paciente = pessoa.id
                            LIMIT 1
                        ) AS datainicial,
                        (select 0) AS antigo
                    from 
                        pedido as pedido_principal
                        left join pessoa on pessoa.id = pedido_principal.id_paciente 
                        left join pedido_planos on pedido_planos.id_pedido = pedido_principal.id
                        left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
                        left join agenda on agenda.id_pedido        = pedido_planos.id_pedido AND
                                            agenda.id_tabela_preco  = pedido_planos.id_plano
                        left join pedido_forma_pag on pedido_forma_pag.id_pedido = pedido_principal.id
                        left join forma_pag on forma_pag.id = pedido_forma_pag.id_forma_pag
                    where 
                        pedido_principal.data_validade >= '". $filtroi->format('Y-m-d') ."'
                        ".$data_final1 ."
                        and pedido_principal.lixeira = 0 
                        and pedido_principal.tipo_contrato = 'N' 
                        and tabela_precos.associado = 'S' 
                        and agenda.status in ('F', 'A')
                        and agenda.lixeira = 0
                        and pedido_principal.id_emp = ". $idempresa ."
                    group by 
                        pedido_principal.id_paciente,
                        forma_pag.descr
                    union all 
                        (
                        select 
                            pessoa.nome_fantasia AS nome, 
                            (
                                select
                                    c2.id
                                from
                                    old_contratos AS c2
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                LIMIT 1
                            ) AS id,
                            (
                                select
                                    GROUP_CONCAT(m2.descr)
                                from
                                    old_atividades
                                    left join old_contratos AS c2 on c2.id = old_atividades.id_contrato
                                    left join old_modalidades AS m2 on m2.id = old_atividades.id_modalidade
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                LIMIT 1
                            ) AS plano,
                            pessoa.celular1 AS celular, 
                            MAX(old_contratos.datafinal) AS data_validade,
                            (
                                select
                                    c2.id_periodo_contrato
                                from
                                    old_contratos AS c2
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                LIMIT 1
                            ),
                            (
                                select
                                    SUM(old_atividades.qtd_ini)
                                from
                                    old_atividades
                                    left join old_contratos AS c2 on c2.id = old_atividades.id_contrato
                                    left join old_modalidades AS m2 on m2.id = old_atividades.id_modalidade
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                group by old_atividades.id_contrato
                                LIMIT 1
                            ) AS max_qtde,
                            (
                                select
                                    SUM(old_atividades.qtd)
                                from
                                    old_atividades
                                    left join old_contratos AS c2 on c2.id = old_atividades.id_contrato
                                    left join old_modalidades AS m2 on m2.id = old_atividades.id_modalidade
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                group by old_atividades.id_contrato
                                LIMIT 1
                            ) AS atv_consu,
                            MAX(old_plano_pagamento.descr) AS forma_pag,
                            (
                                select
                                    c2.datainicial
                                from
                                    old_atividades
                                    left join old_contratos AS c2 on c2.id = old_atividades.id_contrato
                                    left join old_modalidades AS m2 on m2.id = old_atividades.id_modalidade
                                where
                                    c2.datafinal = MAX(old_contratos.datafinal) AND
                                    c2.pessoas_id = old_contratos.pessoas_id
                                LIMIT 1
                            ) AS datainicial,
                            (select 1) AS antigo
                        from 
                            old_contratos 
                            left join pessoa on pessoa.id = old_contratos.pessoas_id 
                            left join old_atividades on old_atividades.id_contrato = old_contratos.id
                            left join old_modalidades on old_modalidades.id = old_atividades.id_modalidade
                            left join old_finanreceber on old_finanreceber.id_contrato = old_contratos.id
                            left join old_plano_pagamento on old_plano_pagamento.id = old_finanreceber.id_planopagamento
                        where 
                            datafinal >= '". $filtroi->format('Y-m-d') ."'
                            ". $data_final2 ."
                            and tipo_contrato = '' 
                            and id_periodo_contrato not in (1,7)
                            and old_contratos.id_emp = ".$idempresa."
                        group by 
                            old_contratos.pessoas_id
                        )
                    ) AS tab_aux
                    WHERE 
                        atv_consu > 0
                    ". $filtro ."
                group by
                    nome, 
                    celular,
                    id
                order by
                    nome
              "
            )
        );
        // return $ativos;
        return view('.reports.impresso_cockpit', compact('ativos', 'filtroi', 'filtrof', 'idempresa'));
    }

}