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

class ResumoContratosVendasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {   
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();
        $tabela_precos = DB::select(
                        DB::raw("
                            select * from tabela_precos where lixeira = 0 AND status = 'A'
                        ")
                    );
        return view("resumo_contratos_vendas", compact('empresas', 'tabela_precos'));
    }


    public function imprimir($id_emp, $agrupamento, $dinicial, $dfinal, $modalidade, $exibirF){
        $datainicial = new \Datetime($dinicial);
        $datafinal = new \DateTime($dfinal);

        $data_inicial = $datainicial->format('d/m/Y');
        $data_final = $datafinal->format('d/m/Y');

        $query = "
        select 
            GROUP_CONCAT(contratos) as contratos,
            descr,
            SUM(total) AS total,
            SUM(qtd) AS qtd
        from    
            (
                select 
                    GROUP_CONCAT(pedido.id) AS contratos,
                    tabela_precos.descr AS descr,
                    SUM(pedido_planos.valor * (ifnull(ped_forma_pag.valtot, 0) / pedido.total)) AS total,
                    SUM(pedido_planos.qtde) AS qtd
                from 
                    pedido 
                    left join pessoa on pessoa.id = pedido.id_paciente
                    left join (SELECT pedido_forma_pag.id_pedido,
                                      SUM(pedido_forma_pag.valor_total) as valtot
                               FROM pedido_forma_pag
                               WHERE (pedido_forma_pag.id_forma_pag not in (8,11,99,101) 
                                  OR pedido_forma_pag.id_forma_pag is null)
                               GROUP BY pedido_forma_pag.id_pedido
                    ) AS ped_forma_pag on ped_forma_pag.id_pedido = pedido.id
                    left join pedido_planos on pedido_planos.id_pedido = pedido.id
                    left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
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
                where
                    pedido.status = 'F' AND
                    (pedido.total is not null and pedido.total > 0) AND
                    pedido.lixeira = 0 AND
                    pedido.id_emp = ". $id_emp ." AND
                    pedido.data >= '". $datainicial->format('Y-m-d') ."' AND
                    pedido.data <= '". $datafinal->format('Y-m-d')   ."'";
        if ($exibirF == "S") $query .= " AND tabAux.qtd > 0";
        $query .= "
                group by
                    tabela_precos.descr
                 union all (
                     select
                         (select '') AS contratos,
                         old_faturamento_view.modalidade,
                         SUM(old_faturamento_view.total) AS total,
                         SUM(tab.qtd_total) AS qtd
                     from
                         old_faturamento_view
                         left join (SELECT 
                                        old_atividades.id_contrato,
                                        SUM(old_atividades.qtd) as qtd_total
                                    FROM
                                        old_atividades
                                    GROUP BY
                                        old_atividades.id_contrato
                         ) as tab on tab.id_contrato = old_faturamento_view.id_contrato
                     where
                         old_faturamento_view.datainicial >= '". $datainicial->format('Y-m-d') ."' AND
                         old_faturamento_view.datainicial <= '". $datafinal->format('Y-m-d') ."'
                     group by 
                         old_faturamento_view.modalidade
                 )
             ) AS aux
             group by descr
             order by total DESC
         ";

        $modalidades = DB::select(DB::raw($query));
    
        $valor_total = 0;
        foreach($modalidades AS $modalidade) {
            $valor_total += $modalidade->total;
        }

        
        return view('.reports.impresso_resumo_contratos_vendas', compact("data_inicial",
                                                                "data_final",
                                                                "modalidades",
                                                                "agrupamento",
                                                                "tabela_precos", 
                                                                "id_emp",
                                                                "valor_total"));
    }

}
