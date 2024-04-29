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

class ContratosPorPeriodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {   
        $membros = DB::table("pessoa")
                   ->where(function($sql){
                        $sql->where('colaborador', 'R')
                            ->orWhere('colaborador', 'A');
                   })
                   ->where('lixeira', '<>', 1)
                   ->where('id', '<>', 1)
                   ->where(function($sql) {
                        if (Pessoa::find(Auth::user()->id_profissional)->id == 28480001071) {
                            $sql->whereRaw("pessoa.id IN (28480003163, 28480003540, 28480002247, 28480002672, 28480002568, 28480001071, 28480002960)");
                        } else if (Pessoa::find(Auth::user()->id_profissional)->administrador <> 'S'){
                            $sql->where('pessoa.id', Auth::user()->id_profissional);
                        }
                   })
                   ->orderBy('nome_fantasia')
                   ->get();
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view("contratos_por_periodo", compact('membros', 'empresas'));
    }


    public function imprimir($id_emp, $id_membro, $dinicial, $dfinal, $filtro, $orientacao, $exibirF){
        $datainicial = new \Datetime($dinicial);
        $datafinal = new \DateTime($dfinal);

        $data_inicial = $datainicial->format('d/m/Y');
        $data_final = $datafinal->format('d/m/Y');

        $consultor = '0';

        $query = " SELECT * FROM (
            (SELECT
                pedido.data          AS data_inicial,
                pedido.data_validade AS data_final,
                pedido.id            AS id_contrato,
                
                pessoa.nome_fantasia AS associado,
                
                GROUP_CONCAT( DISTINCT
                    tabela_precos.descr
                )                    AS plano,
                
                UPPER(GROUP_CONCAT(forma_pag.descr)) AS forma_pag,

                SUM(
                    pedido_planos.valor *
                    (ifnull(ped_forma_pag.valtot, 0) / pedido.total) *
                    multiplicador.num
                ) AS valor,
                SUM(
                    pedido_planos.valor *
                    (ifnull(ped_forma_pag.valtot, 0) / pedido.total) *
                    multiplicador.num
                ) AS total,
                
                0                    AS antigo
            
            FROM pedido
            
            LEFT JOIN pessoa
                ON pessoa.id = pedido.id_paciente

            LEFT JOIN pedido_forma_pag
                ON pedido_forma_pag.id_pedido = pedido.id
            
            LEFT JOIN (SELECT pedido_forma_pag.id_pedido, pedido_forma_pag.id_forma_pag,
                                    SUM(pedido_forma_pag.valor_total) as valtot
                            FROM pedido_forma_pag
                            WHERE (pedido_forma_pag.id_forma_pag not in (8,11,99,101,103) 
                                OR pedido_forma_pag.id_forma_pag is null)
                            GROUP BY pedido_forma_pag.id_pedido, pedido_forma_pag.id_forma_pag
            ) AS ped_forma_pag on ped_forma_pag.id_pedido = pedido.id and ped_forma_pag.id_forma_pag = pedido_forma_pag.id_forma_pag

            LEFT JOIN forma_pag
                ON forma_pag.id = pedido_forma_pag.id_forma_pag

            LEFT JOIN pedido_planos
                ON pedido_planos.id_pedido = pedido.id
            
            LEFT JOIN tabela_precos
                ON tabela_precos.id = pedido_planos.id_plano
            
            left join (SELECT p2.id_plano AS id, 
                                    COUNT(procedimento.id) AS qtd
                            FROM
                                    pedido_planos As p2
                                    left join modalidades_por_plano on modalidades_por_plano.id_tabela_preco = p2.id_plano
                                    left join procedimento on procedimento.id = modalidades_por_plano.id_procedimento
                            WHERE
                                    procedimento.faturar = 1
                            GROUP BY
                                    p2.id_plano
                ) AS tabAux on tabAux.id = tabela_precos.id

            LEFT JOIN (
                    SELECT
                        pedido_planos.id_pedido AS id_pedido,
                        pedido_planos.id_plano AS id_plano,
                        IFNULL((
                            IFNULL(ger.ct, 0) / (IFNULL(tot.ct, 0))
                        ), 1) AS num

                    FROM pedido_planos

                    JOIN pedido
                        ON pedido.id = pedido_planos.id_pedido

                    LEFT JOIN (
                        SELECT
                            COUNT(*) AS ct,
                            id_pedido,
                            tabAux1.id_plano

                        FROM (
                            SELECT
                                agenda.id_pedido,
                                agenda.id_tabela_preco AS id_plano,
                                agenda.lixeira AS lixeira1,
                                pedido.data,
                                pessoa.gera_faturamento,
                                pessoa.d_naofaturar,
                                pessoa.lixeira AS lixeira2

                            FROM agenda

                            LEFT JOIN pessoa
                                ON pessoa.id = agenda.id_profissional

                            LEFT JOIN pedido
                                ON pedido.id = agenda.id_pedido
                        ) AS tabAux1 

                        WHERE tabAux1.lixeira1 = 0 AND tabAux1.lixeira2 = 0 AND (
                            tabAux1.gera_faturamento = 'S' 
                         OR tabAux1.data < tabAux1.d_naofaturar
                         OR tabAux1.gera_faturamento IS NULL
                        )

                        GROUP BY id_pedido, id_plano
                    ) AS ger ON ger.id_pedido = pedido.id AND ger.id_plano = pedido_planos.id_plano

                    LEFT JOIN (
                        SELECT
                            COUNT(*) AS ct,
                            agenda.id_pedido,
                            agenda.id_tabela_preco AS id_plano

                        FROM agenda

                        WHERE agenda.lixeira = 0

                        GROUP BY id_pedido, id_plano
                    ) AS tot ON tot.id_pedido = pedido.id AND tot.id_plano = pedido_planos.id_plano
                ) AS multiplicador ON multiplicador.id_pedido = pedido.id
                                  AND multiplicador.id_plano  = pedido_planos.id_plano
                
            WHERE
                (pedido.total is not null and pedido.total > 0) AND
                pedido.lixeira =   0                                     AND
                pedido.id_emp  =   ".$id_emp."                           AND
                pedido.data    >=  '".($datainicial->format("Y-m-d"))."' AND
                pedido.data    <=  '".($datafinal->format("Y-m-d"))."'";
        
        if ($id_membro <> 0) $query .= " AND id_prof_exa = ".$id_membro;

        $query .= " AND pedido.status = 'F' ";

        switch($filtro) {
            case "C":
                $query .= " AND pedido.id_convenio <> 0";
                break;
            case "P":
                $query .= " AND
                ( 
                    pedido.id_convenio IS NULL
                OR
                    pedido.id_convenio = 0
                )
                ";
                break;
        }

        if ($exibirF == "S") $query .= " AND tabAux.qtd > 0";
        
        $query .= "
            GROUP BY
                pedido.id,
                pedido.data,
                pedido.data_validade,
                pessoa.nome_fantasia,
                pedido.total

            ORDER BY
                pedido.data
            )

            UNION ALL

                (SELECT
                    old_faturamento_view.datainicial AS data_inicial,
                    
                    old_contratos.datafinal          AS data_final,
                    
                    old_faturamento_view.id_contrato AS id_contrato,
                    old_faturamento_view.pessoa_nome AS associado,
                    old_faturamento_view.modalidade  AS plano,
                    ''                               AS forma_pag,
                    old_faturamento_view.valor       AS valor,
                    old_faturamento_view.total       AS total,

                    1                                AS antigo
                
                FROM old_faturamento_view

                LEFT JOIN old_contratos
                    ON old_contratos.id = old_faturamento_view.id_contrato
                
                LEFT JOIN old_finanreceber
                    ON old_finanreceber.id_contrato = old_contratos.id
                
                LEFT JOIN old_financeira
                    ON old_financeira.id = old_finanreceber.id_financeira
                
                LEFT JOIN old_mov_atividades
                    ON old_mov_atividades.id = old_contratos.id_agendamento
                
                WHERE
                    old_faturamento_view.datainicial >= '".$datainicial->format("Y-m-d")."' AND
                    old_faturamento_view.datainicial <= '".$datafinal->format("Y-m-d")."'";
                
        if ($id_membro != 0 && $id_membro != '0') $query .= " AND old_contratos.consultor_id = ".$id_membro;

        if ($filtro == "C" || $filtro == "P") {
            $query .= " AND old_financeira.convenio ";
            $query .= $filtro == "C" ? "=" : "<>";
            $query .= " 'S'";
        }

        $query .= "
            GROUP BY
                old_faturamento_view.datainicial,
                old_contratos.datafinal,
                old_faturamento_view.id_contrato,
                old_faturamento_view.pessoa_nome,
                old_faturamento_view.modalidade,
                old_faturamento_view.valor,
                old_faturamento_view.total
            
            ORDER BY
                old_faturamento_view.data)
            ) as old
            ORDER BY
                old.data_inicial";
        //return $query;
        $contratos = DB::select(DB::raw($query));

        if ($id_membro != 0 && $id_membro != '0') {
            $consultor = DB::table('pessoa')
                            ->where('id', $id_membro)
                            ->value('nome_fantasia');
        }

        $empresa = DB::table('empresa')->where('id', $id_emp)->value('descr');
        $cidade  = DB::table('empresa')->where('id', $id_emp)->value('cidade');
        //return $query;
        if ($orientacao == 'P'){
            return view('.reports.impresso_contratos_por_periodo', compact('data_inicial',
                                                                           'data_final', 
                                                                           'contratos',
                                                                           'consultor',
                                                                           'empresa',
                                                                           'cidade'));
        }
        else {
            return view('.reports.impresso_contratos_por_periodo2', compact('data_inicial',
                                                                           'data_final', 
                                                                           'contratos',
                                                                           'consultor',
                                                                           'empresa',
                                                                           'cidade'));
        }
    }

}