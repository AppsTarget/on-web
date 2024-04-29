<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\Pedido;
use App\TabelaPrecos;
use App\Pessoas;
use App\Modalidades_por_plano;
use App\Comissao_exclusiva;
use App\Pessoa;
use Illuminate\Http\Request;

class CockpitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function listar(Request $request) {
        return view('cockpit');
    }

    public function grafico1($data){
        $filtroi = new \DateTime(date('Y-m', strtotime($data)) . '-01');
        $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));

        // return $filtrof->format('Y-m-d');
        $faturamento_ar = array();
        for($i = 0; $i < 6; $i++){
            $filtroi2 = new \DateTime(date('Y-m', strtotime($data)) . '-01');;
            $filtroi2->modify('-'.$i.' month');
            $filtrof2 = new \DateTime(date('Y-m-d', strtotime($filtroi2->format('Y-m-d') .' +1 month - 1 day')));
            if (strtotime(date('Y-m-d', strtotime($filtroi2->format('Y-m-d')))) > strtotime('2022-11-01')) {
                $aux = DB::select(DB::raw($this->queryFaturamento(
                    date('Y-m-d', strtotime($filtroi2->format('Y-m-d'))),
                    date('Y-m-d', strtotime($filtrof2->format('Y-m-d'))),
                    false,
                    true,
                    "1"
                )));
                array_push($faturamento_ar, $aux[0]->valTot);
            }
            else {
                $faturamento_mes = DB::select(
                    DB::raw("
                        select 
                            pedido.total AS total
                        from 
                            pedido
                            left join pedido_forma_pag on pedido_forma_pag.id_pedido = pedido.id
                            left join agenda on agenda.id = pedido.id_agendamento
                            left join pessoa as profissional on profissional.id = agenda.id_profissional
                        where
                            ((profissional.gera_faturamento = 'S' 
                            or profissional.d_naofaturar >= pedido.data) OR
                            profissional.id is null) AND
                            pedido.data >= '". date('Y-m-d', strtotime($filtroi->format('Y-m-d')." -". strVal($i) ."months")) ."' AND
                            pedido.data <= '". date('Y-m-d', strtotime($filtrof->format('Y-m-d')." -". strVal($i) ."months")) ."' AND 
                            pedido.status = 'F' AND 
                            pedido.id_emp = ". getEmpresa() ." AND 
                            (pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND 
                            pedido.lixeira = 0
                        group by 
                            pedido.id
                    ")
                );                                    
                $aux = 0;
                foreach($faturamento_mes AS $faturamento){
                $aux += $faturamento->total;
                }
                $faturamento_mes_old = DB::table('old_faturamento_view')
                                ->selectRaw("SUM(old_faturamento_view.total) AS total")
                                ->where('datainicial', '>=', date('Y-m-d', strtotime($filtroi->format('Y-m-d')." -". strval($i) ."months")))
                                ->where('datainicial', '<=', date('Y-m-d', strtotime($filtrof->format('Y-m-d')." -". strval($i) ."months")))
                                ->first();


                                    
                array_push($faturamento_ar, ($aux + $faturamento_mes_old->total));
            }
        }
        return $faturamento_ar;
    }

    public function grafico2($data){
        $filtroi = new \DateTime(date('Y-m', strtotime($data)) . '-01');
        $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));

        // $modalidades_mes = DB::select(
        //     DB::raw("
        //         select
        //             agenda.id_modalidade,
        //             procedimento.descr AS descr_modalidade
        //         from   
        //             pedido
        //             inner join agenda on agenda.id_pedido = pedido.id 
        //             inner join  procedimento on procedimento.id = agenda.id_modalidade
        //         where
        //             pedido.status = 'F' AND 
        //             pedido.data >= '". $filtroi->format('Y-m-d') ."' AND 
        //             pedido.data <= '". $filtrof->format('Y-m-d') ."' AND
        //             pedido.lixeira = 0 AND 
        //             pedido.id_emp = ". getEmpresa() ." 
        //         group by
        //             agenda.id_modalidade,
        //             procedimento.descr 
        //         union all
        //             (
        //                 select 
        //                     procedimento.id AS id_modalidade,
        //                     procedimento.descr AS descr_modalidade
        //                 from
        //                     old_faturamento_view
        //                     inner join old_atividades on old_atividades.id_contrato = old_faturamento_view.id_contrato
        //                     inner join old_modalidades on old_modalidades.id = old_atividades.id_modalidade
        //                     inner join procedimento on procedimento.id = old_modalidades.id_novo
        //                 where
        //                     old_faturamento_view.datainicial >= '". $filtroi->format('Y-m-d') ."' AND
        //                     old_faturamento_view.datainicial <= '". $filtrof->format('Y-m-d') ."' AND 
        //                     old_faturamento_view.id_emp = ". getEmpresa() ."
        //             )"
        //     )
        // );

        // $labels = array();
        // $values = array();
        // foreach($modalidades_mes as $modalidade){
        //     array_push($labels, $modalidade->descr_modalidade);
        //     array_push($values,
        //     sizeof(
        //         DB::select(
        //             DB::raw("
        //                 select 
        //                     procedimento.id AS id_modalidade,
        //                     procedimento.descr AS descr_modalidade
        //                 from
        //                     old_faturamento_view
        //                     inner join old_atividades on old_atividades.id_contrato = old_faturamento_view.id_contrato
        //                     inner join old_modalidades on old_modalidades.id = old_atividades.id_modalidade
        //                     inner join procedimento on procedimento.id = old_modalidades.id_novo
        //                 where 
        //                     old_faturamento_view.datainicial >= '". $filtroi->format('Y-m-d') ."' AND
        //                     old_faturamento_view.datainicial <= '". $filtrof->format('Y-m-d') ."' AND
        //                     procedimento.id = ". $modalidade->id_modalidade ." AND 
        //                     old_faturamento_view.id_emp = ". getEmpresa() ."
        //                 union all
        //                     (
        //                         select 
        //                             agenda.id_modalidade,
        //                             procedimento.descr AS descr_modalidade
        //                         from
        //                             pedido
        //                             inner join agenda on agenda.id_pedido = pedido.id
        //                             inner join procedimento on procedimento.id = agenda.id_modalidade
        //                         where 
        //                             pedido.status = 'F' AND 
        //                             pedido.data >= '".  $filtroi->format('Y-m-d') ."' AND 
        //                             pedido.data <= '". $filtrof->format('Y-m-d')  ."' AND
        //                             procedimento.id = ". $modalidade->id_modalidade ." AND 
        //                             pedido.lixeira = 0 AND 
        //                             pedido.id_emp = ". getEmpresa() . "
        //                     ) 
        //             ")
        //         )
        //     ));

        // }

        $query = DB::select(DB::raw("
            SELECT
                procedimento.descr AS descr_modalidade,
                COUNT(agenda.id) AS quantidade
                
            FROM agenda

            JOIN procedimento
                ON procedimento.id = agenda.id_modalidade

            WHERE agenda.lixeira = 0
              AND procedimento.lixeira = 0
              AND data >= '".$filtroi->format('Y-m-d')."'
              AND data <= '".$filtrof->format('Y-m-d')."'
              AND status = 'F'
              AND agenda.id_emp = ".getEmpresa()."
            
            GROUP BY descr_modalidade
        "));
        
        $labels = array();
        $values = array();
        foreach($query as $linha) {
            array_push($labels, $linha->descr_modalidade);
            array_push($values, $linha->quantidade);
        }
        $data = new \StdClass;
        $data->labels = $labels;
        $data->values = $values;
        return json_encode($data);
    }

    private function queryAssoc($filtro, $id, $completo) {
        $query = "SELECT pessoa.id, ";
        if ($completo) {
            $query .= "
                UPPER(pessoa.nome_fantasia)                    AS nome_fantasia,
                UPPER(IFNULL(pessoa.cidade, 'NÃO CADASTRADO')) AS cidade,
                IFNULL(pessoa.celular1,
                    IFNULL(pessoa.celular2,
                        IFNULL(pessoa.telefone1,
                            IFNULL(pessoa.telefone2,
                            'NÃO CADASTRADO')
                        )
                    )
                ) AS telefone,
                CASE
                    WHEN (dt.num IS NOT NULL) THEN
                        CASE
                            WHEN (dt.num >= 0 AND dt.num <= 150) THEN dt.num
                            ELSE 'ERRO<br>VERIFIQUE DATA DE NASCIMENTO'
                        END
                    ELSE 'NÃO CADASTRADO'
                END AS idade,
                DATE_FORMAT(associado.dt, '%d/%m/%Y') AS vencimento,
            ";
        }
        $query .= "DATE_SUB(associado.dt_min, INTERVAL 1 DAY) AS dt_vigente FROM pessoa ";
        if ($completo) {
            $query .= "
                JOIN (
                    SELECT
                        id,
                        TIMESTAMPDIFF(YEAR, pessoa.data_nasc, CURDATE()) AS num
                    FROM pessoa
                ) AS dt ON pessoa.id = dt.id
            ";
        }
        $selecao = array("id_paciente AS id_pessoa", "COUNT(*) AS num", "MIN(pedido.data) AS dt_min");
        if ($completo) array_push($selecao, "MAX(pedido.data_validade) AS dt");
        $query .= "
            LEFT JOIN (
                SELECT
                    ".implode(", ", $selecao)."
                FROM pedido
                LEFT JOIN pedido_planos
                    ON pedido_planos.id_pedido = pedido.id
                LEFT JOIN tabela_precos
                    ON tabela_precos.id = pedido_planos.id_plano
                WHERE tabela_precos.associado = 'S'
                    AND pedido.lixeira = 0
                    AND pedido.data <= ".$filtro."
                    AND pedido.data_validade >= ".$filtro."
                    AND pedido.id_emp = ".getEmpresa()."
                GROUP BY id_paciente
            ) AS associado ON associado.id_pessoa = pessoa.id
            WHERE (pessoa.paciente = 'S' OR pessoa.colaborador <> 'N')
                AND pessoa.cliente = 'N'
                AND pessoa.lixeira = 0
                AND associado.num IS NOT NULL
        ";
        if ($id > 0) $query .= " AND pessoa.id = ".$id;
        else if ($completo) $query .= " ORDER BY pessoa.nome_fantasia";
        return $query;
    }

    private function obterIECConv($ativos, $filtrof) {
        $pesqIEC = [];
        foreach ($ativos as $ativo) {
            $dataA = idate("m") == $filtrof->format("m") ? date("Y-m-d") : $filtrof->format("Y-m-d");
            $dataB = $ativo->dt_vigente;
            $dataAObj = new \DateTime($dataA);
            $dataBObj = new \DateTime($dataB);
            $dataAObj->modify('-1 month');
            $dataBObj->modify('+1 day');
            $dataAObj = $dataAObj->format("Y-m-d");
            $dataBObj = $dataBObj->format("Y-m-d");
            if ($dataBObj >= $dataAObj && $dataBObj <= $dataA) array_push($pesqIEC, "(id_paciente = ".$ativo->id." AND DATE_SUB('".$dataBObj."', INTERVAL 1 MONTH) < DATE_FORMAT(IEC_pessoa.created_at, '%Y-%m-%d'))");
        }
        if (sizeof($pesqIEC) > 0) {
            $iecConv = DB::select(DB::raw("
                SELECT
                    iec.dt,
                    pessoa.id
                    
                FROM pessoa

                JOIN (
                    SELECT
                        id_paciente,
                        DATE_FORMAT(MAX(IEC_pessoa.created_at), '%d/%m/%Y') AS dt
                    
                    FROM IEC_pessoa
                    
                    LEFT JOIN IEC_questionario 
                        ON IEC_questionario.id = IEC_pessoa.id_questionario
                    
                    WHERE IEC_pessoa.lixeira = 0
                        AND IEC_questionario.ativo = 'S'
                        AND IEC_questionario.lixeira = 'N'
                        AND (".implode(" OR ", $pesqIEC).")
                    
                    GROUP BY id_paciente
                ) AS iec ON iec.id_paciente = pessoa.id

                WHERE pessoa.lixeira = 0
            "));
        }
        $resultado = [];
        foreach ($iecConv as $iec) {
            foreach ($ativos as $ativo) {
                if ($iec->id == $ativo->id) {
                    $aux = $iec->dt;
                    $iec = $ativo;
                    $iec->realizado_em = $aux;
                    array_push($resultado, $iec);
                }
            }
        }
        return $resultado;
    }

    private function queryFaturamento($inicio, $fim, $completo, $total, $filtro) {
        $query = "SELECT ";
        if (!$total) $query .= "Hab, ";
        $query .= $completo ? "Caixa, Contrato, Fim, Inicio, Paciente, Plano, Valor" : "IFNULL(SUM(Valor),0) AS valTot";
        $query .= " FROM (SELECT ";
        if ($completo) {
            $query .= "
                pedido.id            AS Contrato,
                pedido.data          AS Inicio,
                pedido.data_validade AS Fim,
                pedido.consultor     AS Caixa,
        
                pessoa.nome_fantasia AS Paciente,
            ";
            $query .= $total ? "
                GROUP_CONCAT(
                    DISTINCT tabela_precos.descr SEPARATOR ',<br>'
                )
            " : " tabela_precos.descr ";
            $query .= " AS Plano, ";
        }
        if (!$total) $query .= " tabela_precos.habilitacao AS Hab, ";
        $query .= "
                    SUM(
                        pedido_planos.valor *
                        (IFNULL(ped_forma_pag.valtot, 0) / pedido.total) *
                        multiplicador.num
                    ) AS Valor

                FROM pedido

                JOIN pessoa
                    ON pessoa.id = pedido.id_paciente

                LEFT JOIN pedido_planos
                    ON pedido_planos.id_pedido = pedido.id

                LEFT JOIN tabela_precos
                    ON tabela_precos.id = pedido_planos.id_plano

                LEFT JOIN (
                    SELECT
                        pedido_forma_pag.id_pedido,
                        SUM(pedido_forma_pag.valor_total) AS valtot
                        
                    FROM pedido_forma_pag

                    WHERE pedido_forma_pag.id_forma_pag NOT IN (8, 11, 99, 101, 103)
                       OR pedido_forma_pag.id_forma_pag IS NULL

                    GROUP BY pedido_forma_pag.id_pedido
                ) AS ped_forma_pag ON ped_forma_pag.id_pedido = pedido.id

                LEFT JOIN (
                    SELECT
                        p2.id_plano AS id,
                        COUNT(*) AS qtd

                    FROM pedido_planos AS p2

                    LEFT JOIN modalidades_por_plano
                        ON modalidades_por_plano.id_tabela_preco = p2.id_plano

                    LEFT JOIN procedimento
                        ON procedimento.id = modalidades_por_plano.id_procedimento

                    WHERE procedimento.faturar = 1

                    GROUP BY p2.id_plano
                ) AS tabAux ON tabAux.id = tabela_precos.id

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

                WHERE pedido.total IS NOT NULL
                  AND pedido.total > 0
                  AND pedido.lixeira = 0
                  AND pedido.id_emp = ".getEmpresa()."
                  AND pedido.data >= '".$inicio."'
                  AND pedido.data <= '".$fim."'
                  AND pedido.status = 'F'
                  AND tabAux.qtd > 0
                  AND ".$filtro."

                GROUP BY
                    pedido.id,
                    pedido.data,
                    pedido.data_validade,
                    pessoa.nome_fantasia,
        ";
        if ($total) {
            $query .= " pedido.total UNION ALL (SELECT ";
            if ($completo) {
                $query .= "
                            old_contratos.id          AS Contrato,
                            old_contratos.datainicial AS Inicio,
                            old_contratos.datafinal   AS Fim,
                            old_contratos.responsavel AS Caixa,
                    
                            pessoa.nome_fantasia AS Paciente,
                            
                            GROUP_CONCAT(
                                DISTINCT old_modalidades.descr
                            ) AS Plano,
                ";
            }
            $query .= "
                            old_contratos.valor_contrato AS Valor
                        
                        FROM old_contratos
    
                        JOIN old_finanreceber
                            ON old_finanreceber.id_contrato = old_contratos.id
    
                        LEFT JOIN pessoa
                            ON pessoa.id = old_contratos.pessoas_id
                        
                        LEFT JOIN old_atividades
                            ON old_atividades.id_contrato = old_contratos.id
                        
                        LEFT JOIN old_modalidades
                            ON old_modalidades.id = old_atividades.id_modalidade
                        
                        WHERE old_contratos.datainicial >= '".$inicio."'
                          AND old_contratos.datainicial <= '".$fim."'
                          AND old_contratos.situacao = 1
                          AND old_contratos.id_emp = ".getEmpresa()."
                          AND old_finanreceber.id_planopagamento NOT IN (8, 11)
                          AND tipo_contrato <> 'E'
                        
                        GROUP BY
                            old_contratos.id,
                            pessoa.nome_fantasia,
                            old_contratos.datainicial,
                            old_contratos.datafinal,
                            old_contratos.valor_contrato,
                            old_contratos.responsavel
                    )
            ";
            if ($completo) $query .= " ORDER BY Inicio";
            $query .= ") AS aux ";
            if ($completo) {
                $query .= "
                    WHERE aux.Valor > 0
    
                    GROUP BY
                        aux.Caixa,
                        aux.Contrato,
                        aux.Fim,
                        aux.Inicio,
                        aux.Paciente,
                        aux.Plano,
                        aux.Valor
                    
                    ORDER BY
                        Inicio DESC,
                        Fim DESC,
                        Paciente ASC,
                        Plano ASC
                ";
            }   
        } else {
            $query .= "
                    pedido.total,
                    tabela_precos.descr,
                    tabela_precos.habilitacao
                ) AS aux
                
                WHERE aux.Valor > 0
            
                GROUP BY
            ";
            if ($completo) {
                $query .= "
                        aux.Caixa,
                        aux.Contrato,
                        aux.Fim,
                        aux.Inicio,
                        aux.Paciente,
                        aux.Plano,
                        aux.Valor
                
                    ORDER BY
                        Inicio DESC,
                        Fim DESC,
                        Paciente ASC,
                        Plano ASC
                ";
            } else $query .= " aux.Hab ";
        }
        return $query;
    }

    private function queryAluguelFiltrar($inicio, $fim) {
        return "
            SELECT
                SUM(valor_total) AS valTot

            FROM titulos_receber

            JOIN salas
                ON salas.id = titulos_receber.id_sala
            
            WHERE salas.lixeira = 0
              AND titulos_receber.lixeira = 'N'
              AND obs <> 'Contrato cancelado'
              AND d_vencimento >= '".$inicio."'
              AND d_vencimento <= '".$fim."'
              AND id_emp = ".getEmpresa()."
        ";
    }

    private function queryAluguelMostrar($inicio, $fim) {
        return "
            SELECT
                pessoa.nome_fantasia AS membro,

                salas.descr          AS sala,

                titulo.valor_total   AS valor,

                DATE_FORMAT(titulo.d_emissao,    '%d/%m/%Y') AS alugado_em,
                DATE_FORMAT(titulo.d_vencimento, '%d/%m/%Y') AS venc_prox_parc

            FROM (
                SELECT
                    titulos_receber.id_pessoa,
                    titulos_receber.id_sala,
                    titulos_receber.d_emissao,
                    titulos_receber.d_vencimento,
                    titulos_receber.valor_total
                
                FROM titulos_receber
                
                JOIN (
                    SELECT MIN(id) AS min_id
                    
                    FROM titulos_receber
                    
                    WHERE id_sala > 0
                      AND lixeira = 'N'
                      AND obs <> 'Contrato cancelado'
                      AND d_vencimento >= '".$inicio."'
                      AND d_vencimento <= '".$fim."'
                
                    GROUP BY ndoc
                ) AS aux ON aux.min_id = titulos_receber.id
            ) AS titulo

            JOIN salas
                ON salas.id = titulo.id_sala

            JOIN pessoa
                ON pessoa.id = titulo.id_pessoa
                
            WHERE salas.lixeira = 0
              AND salas.id_emp = ".getEmpresa()."

            ORDER BY pessoa.nome_fantasia
        ";
    }

    public function filtrar_data($data) {
        $filtroi = new \DateTime(date('Y-m', strtotime($data)) . '-01');
        $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));
        $data = new \StdClass;

//******************************************************************************************************************\\
//******************************************************************************************************************\\
//**************************************************  ASSOCIADOS  **************************************************\\
//******************************************************************************************************************\\
//******************************************************************************************************************\\
        $filtro = idate("m") == $filtrof->format("m") ? "CURDATE()" : "'".$filtrof->format("Y-m-d")."'";
        $ativos = DB::select(DB::raw($this->queryAssoc($filtro, 0, false)));

        $novos = [];
        $renovados = [];
        $resgatados = [];
        foreach ($ativos as $ativo) {
            $aux = DB::select(DB::raw($this->queryAssoc("DATE_SUB('".$filtrof->format("Y-m-d")."', INTERVAL 1 MONTH)", $ativo->id, false)));
            if (sizeof($aux) > 0) {
                $aux = DB::select(DB::raw($this->queryAssoc("'".$ativo->dt_vigente."'", $ativo->id, false)));
                if (sizeof($aux) > 0) array_push($renovados, $aux[0]);
                else                  array_push($resgatados, $ativo);
            } else array_push($novos, $ativo);
        }
        $perdidos = [];
        $lista = DB::select(DB::raw($this->queryAssoc("DATE_SUB('".$filtrof->format("Y-m-d")."', INTERVAL 1 MONTH)", 0, false)));
        foreach ($lista as $pessoa) {
            $aux = DB::select(DB::raw($this->queryAssoc($filtro, $pessoa->id, false)));
            if (sizeof($aux) == 0) array_push($perdidos, $pessoa);
        }
        $iecs = DB::select(DB::raw("
            SELECT
                pessoa.nome_fantasia       AS nome, 
                IFNULL(pessoa.cidade, '')  AS cidade, 
                pessoa.data_nasc           AS data_nascimento, 
                MIN(IEC_pessoa.created_at) AS realizado_em, 
                MIN(IEC_pessoa.created_at) AS created_at,
                IEC_pessoa.id_paciente
            
            FROM IEC_pessoa

            LEFT JOIN pessoa
                ON pessoa.id = IEC_pessoa.id_paciente

            LEFT JOIN IEC_questionario 
                ON IEC_questionario.id = IEC_pessoa.id_questionario

            WHERE   IEC_pessoa.created_at >= '".$filtroi->format('Y-m-d')."'
                AND IEC_pessoa.created_at <= '".$filtrof->format('Y-m-d')."'
                AND (IEC_pessoa.id_emp = ".getEmpresa()." OR IEC_pessoa.id_emp = 0)
                AND IEC_pessoa.lixeira = 0
                AND IEC_questionario.ativo = 'S'
                AND IEC_questionario.lixeira = 'N'

            GROUP BY
                pessoa.nome_fantasia,
                pessoa.cidade,
                pessoa.data_nasc,
                IEC_pessoa.id_paciente
        "));
        $data->ativos     = sizeof($ativos);
        $data->novos      = sizeof($novos);
        $data->renovados  = sizeof($renovados);
        $data->resgatados = sizeof($resgatados);
        $data->perdidos   = sizeof($perdidos);
        $data->total_iecs = sizeof($iecs);
        $data->iecConv    = sizeof($this->obterIECConv($ativos, $filtrof));
        if ($data->total_iecs > 0) $data->iecPercent = ($data->iecConv / $data->total_iecs) * 100;
        else                       $data->iecPercent = 0;

//************************************************************************************************************************\\
//************************************************************************************************************************\\
//****************************************************  ATENDIMENTOS  ****************************************************\\
//************************************************************************************************************************\\
//************************************************************************************************************************\\

        // AGENDAMENTOS DO DIA \\
        $data->agendamentos_dia = DB::select(
            DB::raw("
                select
                    COUNT(*) AS qtd
                from
                    (
                        select agenda.id from agenda
                        where
                            lixeira = 0 AND
                            data = '". date('Y-m-d') ."' AND
                            agenda.id_emp = ". getEmpresa() ."
                        union all
                            (
                                select id from old_mov_atividades
                                where
                                    lixeira = 0 AND
                                    data = '". date('Y-m-d') ."' AND 
                                    old_mov_atividades.id_emp = ". getEmpresa() ."
                            )
                    ) as tab_aux
            ")
        );
        $data->agendamentos_dia = $data->agendamentos_dia[0]->qtd;
        
        // AGENDAMENTOS CANCELADOS NO DIA \\
        $data->agendamentos_canc_dia = DB::select(
            DB::raw("
                select
                    COUNT(*) as qtd
                from
                    (
                        select agenda.id from agenda
                        where
                            lixeira = 0 AND
                            data = '". date('Y-m-d') ."' AND
                            agenda.id_emp = ". getEmpresa() ." AND
                            agenda.status = 'C'
                        union all
                            (
                                select id from old_mov_atividades
                                where
                                    lixeira = 0 AND
                                    data = '". date('Y-m-d') ."' AND 
                                    old_mov_atividades.id_emp = ". getEmpresa() ." AND
                                    old_mov_atividades.status = 'C'
                            )
                    ) as tab_aux
            ")
        );
        $data->agendamentos_canc_dia = $data->agendamentos_canc_dia[0]->qtd;

        // AGENDAMENTOS ATENDIDOS NO DIA \\
        $data->agendamentos_atend_dia = DB::select(
            DB::raw("
                select
                    COUNT(*) as qtd
                from
                    (
                        select agenda.id from agenda
                        where
                            lixeira = 0 AND
                            data = '". date('Y-m-d') ."' AND
                            agenda.id_emp = ". getEmpresa() ." AND
                            agenda.status = 'F'
                        union all
                            (
                                select id from old_mov_atividades
                                where
                                    lixeira = 0 AND
                                    data = '". date('Y-m-d') ."' AND 
                                    old_mov_atividades.id_emp = ". getEmpresa() ." AND
                                    old_mov_atividades.status = 'F'
                            )
                    ) as tab_aux
            ")
        );
        $data->agendamentos_atend_dia = $data->agendamentos_atend_dia[0]->qtd; 
        
        // AGENDAMENTOS ATENDIDOS NO MES \\
        $data->agendamentos_atend_mes = DB::select(
            DB::raw("
                select
                    COUNT(*) as qtd
                from
                    (
                        select agenda.id from agenda
                        where
                            lixeira = 0 AND
                            data >= '". $filtroi->format('Y-m-d') ."' AND
                            data <= '". $filtrof->format('Y-m-d') ."' AND
                            agenda.id_emp = ". getEmpresa() ." AND
                            agenda.status = 'F'
                        union all
                            (
                                select id from old_mov_atividades
                                where
                                    lixeira = 0 AND
                                    data >= '". $filtroi->format('Y-m-d') ."' AND
                                    data <= '". $filtrof->format('Y-m-d') ."' AND
                                    old_mov_atividades.id_emp = ". getEmpresa() ." AND
                                    old_mov_atividades.status = 'F'
                            )
                    ) AS tab_auc
            ")
        );
        $data->agendamentos_atend_mes = $data->agendamentos_atend_mes[0]->qtd;

        // PESSOAS ATENDIDAS NO MES \\
        $data->pessoas_atend_mes = DB::select(
            DB::raw("
                select
                    COUNT(*) as qtd
                from
                    (
                        select agenda.id_paciente from agenda
                        where
                            lixeira = 0 AND
                            data >= '". $filtroi->format('Y-m-d') ."' AND
                            data <= '". $filtrof->format('Y-m-d') ."' AND
                            agenda.id_emp = ". getEmpresa() ." AND
                            agenda.status = 'F'
                        group by
                            id_paciente
                    ) AS tab_aux
            ")
        );
        $data->pessoas_atend_mes = $data->pessoas_atend_mes[0]->qtd;
        
        // ATENDIMENTOS CORTESIA \\
        $data->atendimentos_cortesia = DB::select(
            DB::raw("
                select
                    COUNT(*) AS qtd
                from
                    (
                        select 
                            agenda.id
                        from
                            agenda
                            inner join pedido_forma_pag on pedido_forma_pag.id_pedido = agenda.id_pedido
                        where
                            data >= '". $filtroi->format('Y-m-d') ."' AND
                            data <= '". $filtrof->format('Y-m-d') ."' AND
                            agenda.status = 'F' AND
                            pedido_forma_pag.id_forma_pag = 11 AND
                            lixeira = 0 AND
                            agenda.id_emp = ". getEmpresa() ."
                        group by 
                            agenda.id
                        union all
                            (
                                select 
                                    old_mov_atividades.id
                                from
                                    old_mov_atividades
                                    left join old_atividades on old_atividades.id = old_mov_atividades.id_atividade 
                                    left join old_finanreceber on old_finanreceber.id_contrato = old_atividades.id_contrato
                                where
                                    data >= '". $filtroi->format('Y-m-d') ."' AND
                                    data <= '". $filtrof->format('Y-m-d') ."' AND
                                    old_mov_atividades.status = 'F' AND
                                    old_finanreceber.id_planopagamento = 11 AND
                                    lixeira = 0 AND
                                    old_mov_atividades.id_emp = ". getEmpresa() ."
                                group by
                                    old_mov_atividades.id
                            )
                        ) AS tab_aux
            ")
        );
        $data->atendimentos_cortesia = $data->atendimentos_cortesia[0]->qtd;
        

            //***************************************************************************\\
            //***************************************************************************\\
            //******************************  FATURAMENTO  ******************************\\
            //***************************************************************************\\
            //***************************************************************************\\
        if (strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01')) {
            // DIA \\
            $faturamento_dia = DB::select(DB::raw($this->queryFaturamento(
                date('Y-m-d'),
                date('Y-m-d'),
                false,
                true,
                "1"
            )));
            $aluguel_dia = DB::select(DB::raw($this->queryAluguelFiltrar(
                date('Y-m-d'),
                date('Y-m-d')
            )));
            $data->aluguel_dia = $aluguel_dia[0]->valTot;
            $data->faturamento_dia = $faturamento_dia[0]->valTot + $aluguel_dia[0]->valTot;
    
            // MÊS \\
            $faturamento_mes = DB::select(DB::raw($this->queryFaturamento(
                $filtroi->format('Y-m-d'),
                $filtrof->format('Y-m-d'),
                false,
                true,
                "1"
            )));
            $aluguel_mes = DB::select(DB::raw($this->queryAluguelFiltrar(
                $filtroi->format('Y-m-d'),
                $filtrof->format('Y-m-d')
            )));
            $data->aluguel_mes = $aluguel_mes[0]->valTot;
            $data->faturamento_mes = $faturamento_mes[0]->valTot + $aluguel_mes[0]->valTot;
            
            // SEMESTRE \\
            $faturamento_semestre = DB::select(DB::raw($this->queryFaturamento(
                date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                $filtrof->format('Y-m-d'),
                false,
                true,
                "1"
            )));
            $aluguel_semestre = DB::select(DB::raw($this->queryAluguelFiltrar(
                date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                $filtrof->format('Y-m-d')
            )));
            $data->aluguel_semestre = $aluguel_semestre[0]->valTot;
            $data->faturamento_semestre = $faturamento_semestre[0]->valTot + $aluguel_semestre[0]->valTot;
        }
        else {
            $faturamento_dia = DB::select(
                DB::raw("
                    select sum(total) from (select
                        pedido.total AS total
                    from   
                        pedido
                        left join pedido_forma_pag on pedido_forma_pag.id_pedido = pedido.id
                        left join agenda on agenda.id = pedido.id_agendamento
                        left join pessoa As profissional on profissional.id = agenda.id_profissional
                    where
                        ((profissional.gera_faturamento = 'S' 
                        or profissional.d_naofaturar >= pedido.data) OR
                        profissional.id is null) AND
                        (pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND
                        pedido.data = date('Y-m-d') AND
                        pedido.status', 'F' AND
                        pedido.lixeira', 0 AND
                        pedido.id_emp = ". getEmpresa() ."
                    group by
                        pedido.id)
                ")
            );

            $data->faturamento_dia = $faturamento_dia[0]->total;


            // MES \\
            $faturamento_mes = DB::select(
                DB::raw("select sum(total) from (
                    select
                        pedido.total AS total
                    from
                        pedido_forma_pag AS pedido_forma_pag.id_pedido = pedido.id
                        left join agenda on agenda.id = pedido.id_agendamento
                        left join pessoa As profissional on profissional.id = agenda.id_profissional
                    where
                        ((profissional.gera_faturamento = 'S' 
                        or profissional.d_naofaturar >= pedido.data) OR
                        profissional.id is null) AND
                        pedido.data >= '". $filtroi->format('Y-m-d') ."' AND
                        pedido.data <= '". $filtrof->format('Y-m-d') ."' AND
                        pedido.status = 'F' AND
                        pedido.id_emp = ". getEmpresa() ." AND
                        (pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND
                        pedido.lixeira = 0
                    group by
                        pedido.id
                )")
            );
            $faturamento_mes_old = DB::table('old_faturamento_view')
                            ->selectRaw("SUM(old_faturamento_view.total) AS total")
                            ->where('datainicial', '>=', $filtroi->format('Y-m-d'))
                            ->where('datainicial', '<=', $filtrof->format('Y-m-d'))
                            ->first();

            $data->faturamento_mes = $faturamento_mes[0]->total + $faturamento_mes_old->total;

            // SEMESTRE \\
            $faturamento_semestre = DB::table("pedido")
                        ->select(DB::raw("pedido.total AS total"))
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                        ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                        ->leftjoin('pessoa As profissional', 'profissional.id', 'agenda.id_profissional')
                        ->whereRaw("((profissional.gera_faturamento = 'S' 
                                    or profissional.d_naofaturar >= pedido.data) OR
                                    profissional.id is null)")
                        ->where('pedido.data', '>=', date('Y-m-d', strtotime($filtroi->format('Y-m-d')." -5 months")))
                        ->where('pedido.data', '<=', $filtrof->format('Y-m-d'))
                        ->where('pedido.status', 'F')
                        ->where('pedido.id_emp', getEmpresa())
                        ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null)')
                        ->where('pedido.lixeira', 0)
                        ->groupBy('pedido.id')
                        ->get('total');
            $aux = 0;
            foreach($faturamento_semestre AS $faturamento){
            $aux += $faturamento->total;
            }
            $faturamento_semestre_old = DB::table('old_faturamento_view')
                        ->selectRaw("SUM(old_faturamento_view.total) AS total")
                        ->where('datainicial', '>=', date('Y-m-d', strtotime($filtroi->format('Y-m-d')." -5 months")))
                        ->where('datainicial', '<=', $filtrof->format('Y-m-d'))
                        ->first();

            $data->faturamento_semestre = $aux + $faturamento_semestre_old->total;
        }

        $data->faturamento_geral_dia = DB::select(DB::raw($this->queryFaturamento(
            date('Y-m-d'),
            date('Y-m-d'),
            false,
            false,
            "1"
        )));
        $data->faturamento_geral_mes = DB::select(DB::raw($this->queryFaturamento(
            $filtroi->format('Y-m-d'),
            $filtrof->format('Y-m-d'),
            false,
            false,
            "1"
        )));
        $data->faturamento_geral_semestre = DB::select(DB::raw($this->queryFaturamento(
            date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
            $filtrof->format('Y-m-d'),
            false,
            false,
            "1"
        )));

        //************************************************************************************************************************\\
        //************************************************************************************************************************\\
        //*****************************************************  FINANCEIRO  *****************************************************\\
        //************************************************************************************************************************\\
        //************************************************************************************************************************\\
        
        // CONTAS A RECEBER \\
        $data->receber_atraso = 0;
        $data->receber_hoje = 0;
        $data->receber_semana = 0;
        $data->receber_mes = 0;

        // CONTAS A PAGAR \\
        $data->pagar_atraso = 0; 
        $data->pagar_hoje = 0;
        $data->pagar_semana = 0;  
        $data->pagar_mes = 0;

        return json_encode($data);
    }

    public function mostrar($value, $data){
        if ($value != "aluguel") {
            $filtroi = new \DateTime(date('Y-m', strtotime($data)) . '-01');
            $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));
            $filtro = idate("m") == $filtrof->format("m") ? "CURDATE()" : "'".$filtrof->format("Y-m-d")."'";
        }
        if (!in_array($value, ["ativos", "novos", "renovados", "resgatados", "iecConv"])) {
            switch($value) {
                case "perdidos":
                    $perdidos = [];
                    $lista = DB::select(DB::raw($this->queryAssoc("DATE_SUB('".$filtrof->format("Y-m-d")."', INTERVAL 1 MONTH)", 0, true)));
                    foreach ($lista as $pessoa) {
                        $aux = DB::select(DB::raw($this->queryAssoc($filtro, $pessoa->id, true)));
                        if (sizeof($aux) == 0) array_push($perdidos, $pessoa);
                    }
                    return $perdidos;
                    break;
                case "iec":
                    return DB::select(DB::raw("
                        SELECT
                            pessoa.nome_fantasia       AS nome, 
                            IFNULL(pessoa.cidade, '')  AS cidade, 
                            pessoa.data_nasc           AS data_nascimento, 
                            DATE_FORMAT(MIN(IEC_pessoa.created_at), '%d/%m/%Y') AS realizado_em, 
                            MIN(IEC_pessoa.created_at) AS created_at,
                            IEC_pessoa.id_paciente,
                            CASE
                                WHEN (dt.num IS NOT NULL) THEN
                                    CASE
                                        WHEN (dt.num >= 0 AND dt.num <= 150) THEN dt.num
                                        ELSE 'ERRO<br>VERIFIQUE DATA DE NASCIMENTO'
                                    END
                                ELSE 'NÃO CADASTRADO'
                            END AS idade
                        
                        FROM IEC_pessoa
        
                        LEFT JOIN pessoa
                            ON pessoa.id = IEC_pessoa.id_paciente
                        
                        JOIN (
                            SELECT
                                id,
                                TIMESTAMPDIFF(YEAR, pessoa.data_nasc, CURDATE()) AS num
                            FROM pessoa
                        ) AS dt ON pessoa.id = dt.id

                        LEFT JOIN IEC_questionario 
                            ON IEC_questionario.id = IEC_pessoa.id_questionario
        
                        WHERE   IEC_pessoa.created_at >= '".$filtroi->format('Y-m-d')."'
                            AND IEC_pessoa.created_at <= '".$filtrof->format('Y-m-d')."'
                            AND (IEC_pessoa.id_emp = ".getEmpresa()." OR IEC_pessoa.id_emp = 0)
                            AND IEC_pessoa.lixeira = 0
                            AND IEC_questionario.ativo = 'S'
                            AND IEC_questionario.lixeira = 'N'
                        
                        GROUP BY
                            pessoa.nome_fantasia,
                            pessoa.cidade,
                            pessoa.data_nasc,
                            IEC_pessoa.id_paciente
                    "));
                    break;
                case 'agendamentos_dia':
                    $agendamentos_dia = DB::table('agenda')
                                        ->selectRaw('TRIM(agenda.id_pedido)            AS Contrato,
                                                TRIM(pedido.data)                 AS realizado_em,
                                                TRIM(agenda.data)                 AS Data,
                                                TRIM(agenda.hora)                 AS Horario,
                                                TRIM(paciente.nome_fantasia)      AS Paciente,
                                                TRIM(profissional.nome_fantasia)  AS Profissional,
                                                TRIM(procedimento.descr_resumida) AS Modalidade,
                                                TRIM(agenda_status.descr)         AS Status')
                                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                        ->leftjoin('pessoa AS paciente',     'paciente.id',     'agenda.id_paciente')
                                        ->leftjoin('procedimento',  'procedimento.id',  'agenda.id_modalidade')
                                        ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                        ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                                        ->where('agenda.lixeira', 0)
                                        ->where('agenda.data', date('Y-m-d'))
                                        ->where('agenda.id_emp', getEmpresa())
                                        ->groupBy('agenda.id_pedido',
                                                  'pedido.data',
                                                  'agenda.data',
                                                  'agenda.hora',
                                                  'paciente.nome_fantasia',
                                                  'profissional.nome_fantasia',
                                                  'procedimento.descr_resumida',
                                                  'agenda_status.descr');
                    return DB::table('old_mov_atividades')
                            ->selectRaw("old_atividades.id_contrato AS Contrato,
                                            CONCAT(old_contratos.datainicial,' ',old_contratos.horainicial) AS realizado_em,
                                            old_mov_atividades.data    AS Data,
                                            grade.hora_inicial         AS Horario,
                                            paciente.nome_fantasia     AS Paciente,
                                            profissional.nome_fantasia AS Profissional,
                                            old_modalidades.descr      AS Modalidade,
                                            agenda_status.descr        AS Status")
                            ->leftjoin('grade', 'grade.id', 'old_mov_atividades.id_grade')
                            ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                            ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                            ->leftjoin('pessoa AS paciente',     'paciente.id',     'old_contratos.pessoas_id')
                            ->leftjoin("agenda_status", 'agenda_status.id', 'old_mov_atividades.id_status')
                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                            ->where('old_mov_atividades.lixeira', 0)
                            ->where('old_mov_atividades.data', date('Y-m-d'))
                            ->where('old_mov_atividades.id_emp', getEmpresa())
                            ->groupBy('old_atividades.id_contrato',
                                      'old_contratos.datainicial',
                                      'old_contratos.horainicial',
                                      'old_mov_atividades.data',
                                      'grade.hora_inicial',
                                      'paciente.nome_fantasia',
                                      'profissional.nome_fantasia',
                                      'old_modalidades.descr',
                                      'agenda_status.descr')
                            ->unionAll($agendamentos_dia)
                            ->where('Data', date('Y-m-d'))
                            ->orderBy('Data', 'DESC')
                            ->orderBy('Horario', 'ASC')
                            ->orderBy('Paciente', 'ASC')
                            ->get();
                    break;  
                case 'agendamentos_canc_dia':
                    $agendamentos_dia = DB::table('agenda')
                                        ->selectRaw('TRIM(agenda.id_pedido)            AS Contrato,
                                                TRIM(pedido.data)                 AS realizado_em,
                                                TRIM(agenda.data)                 AS Data,
                                                TRIM(agenda.hora)                 AS Horario,
                                                TRIM(paciente.nome_fantasia)      AS Paciente,
                                                TRIM(profissional.nome_fantasia)  AS Profissional,
                                                TRIM(procedimento.descr_resumida) AS Modalidade,
                                                TRIM(agenda_status.descr)         AS Status')
                                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                        ->leftjoin('pessoa AS paciente',     'paciente.id',     'agenda.id_paciente')
                                        ->leftjoin('procedimento',  'procedimento.id',  'agenda.id_modalidade')
                                        ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                        ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                                        ->where('agenda.lixeira', 0)
                                        ->where('agenda.data', date('Y-m-d'))
                                        ->where('agenda.id_emp', getEmpresa())
                                        ->where('agenda.status', 'C')
                                        ->groupBy('agenda.id_pedido',
                                                  'pedido.data',
                                                  'agenda.data',
                                                  'agenda.hora',
                                                  'paciente.nome_fantasia',
                                                  'profissional.nome_fantasia',
                                                  'procedimento.descr_resumida',
                                                  'agenda_status.descr'); 
                    return DB::table('old_mov_atividades')
                            ->selectRaw("old_atividades.id_contrato AS Contrato,
                                            CONCAT(old_contratos.datainicial,' ',old_contratos.horainicial) AS realizado_em,
                                            old_mov_atividades.data    AS Data,
                                            grade.hora_inicial         AS Horario,
                                            paciente.nome_fantasia     AS Paciente,
                                            profissional.nome_fantasia AS Profissional,
                                            old_modalidades.descr      AS Modalidade,
                                            agenda_status.descr        AS Status")
                            ->leftjoin('grade', 'grade.id', 'old_mov_atividades.id_grade')
                            ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                            ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                            ->leftjoin('pessoa AS paciente',     'paciente.id',     'old_contratos.pessoas_id')
                            ->leftjoin("agenda_status", 'agenda_status.id', 'old_mov_atividades.id_status')
                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                            ->where('old_mov_atividades.lixeira', 0)
                            ->where('old_mov_atividades.data', date('Y-m-d'))
                            ->where('old_mov_atividades.status', 'C')
                            ->where('old_mov_atividades.id_emp', getEmpresa())
                            ->groupBy('old_atividades.id_contrato',
                                      'old_contratos.datainicial',
                                      'old_contratos.horainicial',
                                      'old_mov_atividades.data',
                                      'grade.hora_inicial',
                                      'paciente.nome_fantasia',
                                      'profissional.nome_fantasia',
                                      'old_modalidades.descr',
                                      'agenda_status.descr')
                            ->unionAll($agendamentos_dia)
                            ->where('Data', date('Y-m-d'))
                            ->orderBy('Data', 'DESC')
                            ->orderBy('Horario', 'ASC')
                            ->orderBy('Paciente', 'ASC')
                            ->get();
                    break;
                case 'agendamentos_atend_dia':
                    $agendamentos_dia = DB::table('agenda')
                                        ->selectRaw('TRIM(agenda.id_pedido)            AS Contrato,
                                                TRIM(pedido.data)                 AS realizado_em,
                                                TRIM(agenda.data)                 AS Data,
                                                TRIM(agenda.hora)                 AS Horario,
                                                TRIM(paciente.nome_fantasia)      AS Paciente,
                                                TRIM(profissional.nome_fantasia)  AS Profissional,
                                                TRIM(procedimento.descr_resumida) AS Modalidade,
                                                TRIM(agenda_status.descr)         AS Status')
                                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                        ->leftjoin('pessoa AS paciente',     'paciente.id',     'agenda.id_paciente')
                                        ->leftjoin('procedimento',  'procedimento.id',  'agenda.id_modalidade')
                                        ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                        ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                                        ->where('agenda.lixeira', 0)
                                        ->where('agenda.data', date('Y-m-d'))
                                        ->where('agenda.id_emp', getEmpresa())
                                        ->where('agenda.status', 'F')
                                        ->groupBy('agenda.id_pedido',
                                                  'pedido.data',
                                                  'agenda.data',
                                                  'agenda.hora',
                                                  'paciente.nome_fantasia',
                                                  'profissional.nome_fantasia',
                                                  'procedimento.descr_resumida',
                                                  'agenda_status.descr');
                    return DB::table('old_mov_atividades')
                            ->selectRaw("old_atividades.id_contrato AS Contrato,
                                            CONCAT(old_contratos.datainicial,' ',old_contratos.horainicial) AS realizado_em,
                                            old_mov_atividades.data    AS Data,
                                            grade.hora_inicial         AS Horario,
                                            paciente.nome_fantasia     AS Paciente,
                                            profissional.nome_fantasia AS Profissional,
                                            old_modalidades.descr      AS Modalidade,
                                            agenda_status.descr        AS Status")
                            ->leftjoin('grade', 'grade.id', 'old_mov_atividades.id_grade')
                            ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                            ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                            ->leftjoin('pessoa AS paciente',     'paciente.id',     'old_contratos.pessoas_id')
                            ->leftjoin("agenda_status", 'agenda_status.id', 'old_mov_atividades.id_status')
                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                            ->where('old_mov_atividades.lixeira', 0)
                            ->where('old_mov_atividades.data', date('Y-m-d'))
                            ->where('old_mov_atividades.id_emp', getEmpresa())
                            ->where('old_mov_atividades.status', 'F')
                            ->groupBy('old_atividades.id_contrato',
                                      'old_contratos.datainicial',
                                      'old_contratos.horainicial',
                                      'old_mov_atividades.data',
                                      'grade.hora_inicial',
                                      'paciente.nome_fantasia',
                                      'profissional.nome_fantasia',
                                      'old_modalidades.descr',
                                      'agenda_status.descr')
                            ->unionAll($agendamentos_dia)
                            ->where('Data', date('Y-m-d'))
                            ->orderBy('Data', 'DESC')
                            ->orderBy('Horario', 'ASC')
                            ->orderBy('Paciente', 'ASC')
                            ->get();
                    break;
                case 'agendamentos_atend_mes':
                    $agendamentos_dia = DB::table('agenda')
                                        ->selectRaw('TRIM(agenda.id_pedido)            AS Contrato,
                                                TRIM(pedido.data)                 AS realizado_em,
                                                TRIM(agenda.data)                 AS Data,
                                                TRIM(agenda.hora)                 AS Horario,
                                                TRIM(paciente.nome_fantasia)      AS Paciente,
                                                TRIM(profissional.nome_fantasia)  AS Profissional,
                                                TRIM(procedimento.descr_resumida) AS Modalidade,
                                                TRIM(agenda_status.descr)         AS Status')
                                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                        ->leftjoin('pessoa AS paciente',     'paciente.id',     'agenda.id_paciente')
                                        ->leftjoin('procedimento',  'procedimento.id',  'agenda.id_modalidade')
                                        ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                        ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                                        ->where('agenda.lixeira', 0)
                                        ->where('agenda.data', ">=", $filtroi->format('Y-m-d'))
                                        ->where('agenda.data', "<=", $filtrof->format('Y-m-d'))
                                        ->where('agenda.id_emp', getEmpresa())
                                        ->where('agenda.status', 'F')
                                        ->groupBy('agenda.id_pedido',
                                                  'pedido.data',
                                                  'agenda.data',
                                                  'agenda.hora',
                                                  'paciente.nome_fantasia',
                                                  'profissional.nome_fantasia',
                                                  'procedimento.descr_resumida',
                                                  'agenda_status.descr');
                    return DB::table('old_mov_atividades')
                            ->selectRaw("old_atividades.id_contrato AS Contrato,
                                            CONCAT(old_contratos.datainicial,' ',old_contratos.horainicial) AS realizado_em,
                                            old_mov_atividades.data    AS Data,
                                            grade.hora_inicial         AS Horario,
                                            paciente.nome_fantasia     AS Paciente,
                                            profissional.nome_fantasia AS Profissional,
                                            old_modalidades.descr      AS Modalidade,
                                            agenda_status.descr        AS Status")
                            ->leftjoin('grade', 'grade.id', 'old_mov_atividades.id_grade')
                            ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                            ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                            ->leftjoin('pessoa AS paciente',     'paciente.id',     'old_contratos.pessoas_id')
                            ->leftjoin("agenda_status", 'agenda_status.id', 'old_mov_atividades.id_status')
                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                            ->where('old_mov_atividades.lixeira', 0)
                            ->where('old_mov_atividades.data','>=' , $filtroi->format('Y-m-d'))
                            ->where('old_mov_atividades.data','<=' , $filtrof->format('Y-m-d'))
                            ->where('old_mov_atividades.id_emp', getEmpresa())
                            ->where('old_mov_atividades.status', 'F')
                            ->groupBy('old_atividades.id_contrato',
                                      'old_contratos.datainicial',
                                      'old_contratos.horainicial',
                                      'old_mov_atividades.data',
                                      'grade.hora_inicial',
                                      'paciente.nome_fantasia',
                                      'profissional.nome_fantasia',
                                      'old_modalidades.descr',
                                      'agenda_status.descr')
                            ->unionAll($agendamentos_dia)
                            ->orderBy('Data', 'DESC')
                            ->orderBy('Horario', 'ASC')
                            ->orderBy('Paciente', 'ASC')
                            ->get();
                    break;
                case 'pessoas_atend_mes':
                    return DB::table('agenda')
                           ->select('pessoa.id            AS id_pessoa',
                                    'pessoa.nome_fantasia AS nome')
                           ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                           ->where('agenda.data', '>=', $filtroi)
                           ->where('agenda.data', '<=', $filtrof)
                           ->where('agenda.status', 'F')
                           ->where('agenda.id_emp', getEmpresa())
                           ->where('agenda.lixeira', 0)
                           ->groupBy('pessoa.id', 'pessoa.nome_fantasia')
                           ->orderBy('nome', 'ASC')
                           ->get();
                    break;
                case 'pessoas_atend_cortesia':
                    return DB::select(DB::raw("
                        SELECT
                            UPPER(pessoa.nome_fantasia) AS nome,
                            DATE_FORMAT(agenda.data, '%d/%m/%Y') as data
                        FROM agenda
                        LEFT JOIN pessoa
                            ON pessoa.id = agenda.id_paciente
                        WHERE agenda.id IN (
                            SELECT agenda.id
                            FROM agenda
                            JOIN pedido_forma_pag
                                ON pedido_forma_pag.id_pedido = agenda.id_pedido
                            WHERE data >= '".$filtroi->format('Y-m-d')."'
                            AND data <= '".$filtrof->format('Y-m-d')."'
                            AND agenda.status = 'F'
                            AND pedido_forma_pag.id_forma_pag = 11
                            AND lixeira = 0
                            AND agenda.id_emp = ".getEmpresa()."
                            GROUP BY agenda.id
                            UNION ALL (
                                SELECT old_mov_atividades.id
                                FROM old_mov_atividades
                                LEFT JOIN old_atividades
                                    ON old_atividades.id = old_mov_atividades.id_atividade
                                LEFT JOIN old_finanreceber
                                    ON old_finanreceber.id_contrato = old_atividades.id_contrato
                                WHERE data >= '".$filtroi->format('Y-m-d')."'
                                AND data <= '".$filtrof->format('Y-m-d')."'
                                AND old_mov_atividades.status = 'F'
                                AND old_finanreceber.id_planopagamento = 11
                                AND lixeira = 0
                                AND old_mov_atividades.id_emp = ".getEmpresa()."
                                GROUP BY old_mov_atividades.id
                            )
                        )
                        ORDER BY data DESC
                    "));
                    break;
                case 'faturamento_dia':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date('Y-m-d'),
                        date('Y-m-d'), 
                        true,
                        true,
                        "1"
                    )));
                    break;
                case 'faturamento_mes':
                    return DB::select(DB::raw($this->queryFaturamento(
                        $filtroi->format('Y-m-d'),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "1"
                    )));
                    break;
                case 'faturamento_semestre':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "1"
                    )));
                    break;
                case 'faturamento_hab_dia':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date('Y-m-d'),
                        date('Y-m-d'), 
                        true,
                        true,
                        "tabela_precos.habilitacao = 1"
                    )));
                    break;
                case 'faturamento_hab_mes':
                    return DB::select(DB::raw($this->queryFaturamento(
                        $filtroi->format('Y-m-d'),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "tabela_precos.habilitacao = 1"
                    )));
                    break;
                case 'faturamento_hab_semestre':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "tabela_precos.habilitacao = 1"
                    )));
                    break;
                case 'faturamento_reab_dia':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date('Y-m-d'),
                        date('Y-m-d'), 
                        true,
                        true,
                        "tabela_precos.reabilitacao = 1"
                    )));
                    break;
                case 'faturamento_reab_mes':
                    return DB::select(DB::raw($this->queryFaturamento(
                        $filtroi->format('Y-m-d'),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "tabela_precos.reabilitacao = 1"
                    )));
                    break;
                case 'faturamento_reab_semestre':
                    return DB::select(DB::raw($this->queryFaturamento(
                        date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                        $filtrof->format('Y-m-d'),
                        true,
                        true,
                        "tabela_precos.reabilitacao = 1"
                    )));
                    break;
                case "aluguel_dia":
                    return DB::select(DB::raw($this->queryAluguelMostrar(
                        date('Y-m-d'),
                        date('Y-m-d')
                    )));
                    break;
                case "aluguel_mes":
                    return DB::select(DB::raw($this->queryAluguelMostrar(
                        $filtroi->format('Y-m-d'),
                        $filtrof->format('Y-m-d')
                    )));
                    break;
                case "aluguel_semestre":
                    return DB::select(DB::raw($this->queryAluguelMostrar(
                        date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')),
                        $filtrof->format('Y-m-d')
                    )));
                    break;
                case "aluguel":
                    return DB::select(DB::raw("
                        SELECT
                            aux1.ndoc,
                            pessoa.nome_fantasia AS alugado_por,
                            DATE_FORMAT(titulos_receber.d_emissao, '%d/%m/%Y') AS alugado_em,
                            COUNT(titulos_receber.id) AS parcelas,
                            MAX(titulos_receber.valor_total) AS valor,
                            SUM(titulos_receber.valor_total) AS valor_total,
                            IFNULL(aux2.soma, 0) AS recebido,
                            CASE
                                WHEN titulos_receber.obs = 'Contrato cancelado' THEN 'CANCELADO'
                                WHEN CURDATE() > aux1.venc THEN 'ENCERRADO'
                                ELSE 'ATIVO'
                            END AS situacao
                        
                        FROM titulos_receber
                        
                        JOIN (
                            SELECT
                                ndoc,
                                GROUP_CONCAT(id) AS lista,
                                MAX(d_vencimento) AS venc
                        
                            FROM titulos_receber
                        
                            WHERE id_sala = ".$data."
                              AND lixeira = 'N'
                        
                            GROUP BY ndoc
                        ) AS aux1 ON FIND_IN_SET(titulos_receber.id, aux1.lista) > 0
                        
                        LEFT JOIN (
                            SELECT
                                ndoc,
                                GROUP_CONCAT(id) AS lista,
                                SUM(valor_total_pago) AS soma
                        
                            FROM titulos_receber
                        
                            WHERE lixeira = 'N'
                              AND obs <> 'Contrato cancelado'
                        
                            GROUP BY ndoc
                        ) AS aux2 ON FIND_IN_SET(titulos_receber.id, aux2.lista) > 0
                        
                        JOIN pessoa
                            ON pessoa.id = titulos_receber.id_pessoa
                        
                        GROUP BY
                            ndoc,
                            situacao,
                            alugado_por,
                            alugado_em,
                            recebido,
                            situacao
                        
                        ORDER BY ndoc DESC
                    "));
                    break;
            }
        } else {
            $ativos = DB::select(DB::raw($this->queryAssoc($filtro,0,true)));
            if (!in_array($value, ["ativos", "iecConv"])) {
                $novos = [];
                $renovados = [];
                $resgatados = [];
                foreach ($ativos as $ativo) {
                    $aux = DB::select(DB::raw($this->queryAssoc("DATE_SUB('".$filtrof->format("Y-m-d")."', INTERVAL 1 MONTH)", $ativo->id, true)));
                    if (sizeof($aux) > 0) {
                        $aux = DB::select(DB::raw($this->queryAssoc("'".$ativo->dt_vigente."'", $ativo->id, true)));
                        if (sizeof($aux) > 0) array_push($renovados, $aux[0]);
                        else                  array_push($resgatados, $ativo);
                    } else array_push($novos, $ativo);
                }
                switch ($value) {
                    case "novos":
                        return $novos;
                        break;
                    case "renovados":
                        return $renovados;
                        break;
                    case "resgatados":
                        return $resgatados;
                        break;
                }
            } else if ($value == "iecConv") return $this->obterIECConv($ativos, $filtrof);
            else return $ativos;
        }
    }

    public function exibir_finalizacao($value, $data) {
        $filtroi = new \DateTime(date('Y-m', strtotime($data)) . '-01');
        $filtrof = new \DateTime(date('Y-m-d', strtotime($filtroi->format('Y-m-d') .' +1 month - 1 day')));

        switch($value){
            case 'faturamento_dia':
                // return strtotime($filtroi->format('Y-m-d'));
                // return json_encode(strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01'));
                if (strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01')) {
                    $faturamento_mes = DB::table('pedido')
                                    ->select(DB::raw("pedido.id AS Contrato"),
                                            DB::raw("pessoa.nome_fantasia AS Paciente"),
                                            DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                            DB::raw("pedido.created_at AS Inicio"),
                                            DB::raw("pedido.data_validade AS Fim"),
                                            DB::raw("pedido.total AS Valor"),
                                            DB::raw("pedido.consultor AS Caixa"))
                                    ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                    ->leftjoin('pessoa AS consultor', 'pessoa.id', 'pedido.id_prof_exa')
                                    ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                    ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                    ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                    ->whereRaw("((profissional.gera_faturamento = 'S' 
                                                    or profissional.d_naofaturar >= pedido.data) OR
                                                    profissional.id is null)")
                                    ->where('pedido.status', 'F')
                                    ->whereRaw('((pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND (pedido.total is not null and pedido.total > 0))')
                                    ->where('pedido.lixeira', 0)
                                    ->where('pedido.id_emp', getEmpresa())
                                    ->where('pedido.data', date('Y-m-d'))
                                    // ->where('pedido.id', 2048)
                                    ->groupBy("pedido.id",
                                            "pessoa.nome_fantasia",
                                            "pedido.data",
                                            "pedido.data_validade",
                                            "pedido.total")
                                    ->unionAll(
                                        DB::table("old_contratos")
                                            ->select("old_contratos.id AS Contrato",
                                                        "pessoa.nome_fantasia AS Paciente",
                                                        DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                        "old_contratos.datainicial AS Inicio",
                                                        "old_contratos.datafinal AS Fim",
                                                        "old_contratos.valor_contrato AS Valor",
                                                        "old_contratos.responsavel AS Caixa")
                                            ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                            ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                            ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                            ->where('old_contratos.datainicial', date('Y-m-d'))
                                            ->where('old_contratos.situacao', '1')
                                            ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                            ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                            ->where('tipo_contrato', '<>', 'E')
                                            ->where('old_contratos.id_emp', getEmpresa())
                                            ->groupBy("old_contratos.id",
                                                    "pessoa.nome_fantasia",
                                                    "old_contratos.datainicial",
                                                    "old_contratos.datafinal",
                                                    "old_contratos.valor_contrato",
                                                    "old_contratos.responsavel")
                                    )
                                    ->orderBy('Inicio')
                                    ->get();

                    foreach($faturamento_mes AS $faturamento) {
                        $aux_agendamentos = DB::select(DB::raw(
                            "SELECT 
                                pedido_planos.valor,
                                tabela_precos.descr
                            FROM
                                agenda
                                inner join pedido_planos on pedido_planos.id_pedido = agenda.id_pedido
                                                        and pedido_planos.id_plano  = agenda.id_tabela_preco
                                inner join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                                inner join pessoa        on pessoa.id               = agenda.id_profissional
                            WHERE
                                pedido_planos.id_pedido = ". $faturamento->Contrato . " AND 
                                agenda.lixeira = 0                                      AND 
                                agenda.status = 'F'                                     AND 
                                pessoa.gera_faturamento = 'N'" 
                        ));
                        if (sizeof($aux_agendamentos) > 0) {
                            $faturamento->Plano = str_replace($aux_agendamentos[0]->descr.",",'', $faturamento->Plano);
                            $faturamento->Valor -= $aux_agendamentos[0]->valor;
                        }
                    }
                }
                else {
                    return DB::table('pedido')
                            ->select(DB::raw("pedido.id AS Contrato"),
                                    DB::raw("pessoa.nome_fantasia AS Paciente"),
                                    DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                    DB::raw("pedido.created_at AS Inicio"),
                                    DB::raw("pedido.data_validade AS Fim"),
                                    DB::raw("pedido.total AS Valor"),
                                    DB::raw("pedido.consultor AS Caixa"))
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                            ->join('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                            ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                            ->whereRaw("((profissional.gera_faturamento = 'S' 
                                        or profissional.d_naofaturar >= pedido.data) OR
                                        profissional.id is null)")
                            ->where('pedido.data', date('Y-m-d'))
                            ->where('pedido.status', 'F')
                            ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null)')
                            ->where('pedido.lixeira', 0)
                            ->where('pedido.id_emp', getEmpresa())
                            ->groupBy("pedido.id",
                                    "pessoa.nome_fantasia",
                                    "pedido.data",
                                    "pedido.data_validade",
                                    "pedido.total")
                            ->unionAll(
                                DB::table("old_contratos")
                                    ->select("old_contratos.id AS Contrato",
                                                "pessoa.nome_fantasia AS Paciente",
                                                DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                "old_contratos.datainicial AS Inicio",
                                                "old_contratos.datafinal AS Fim",
                                                "old_contratos.valor_contrato AS Valor",
                                                "old_contratos.responsavel AS Caixa")
                                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                    ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                    ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                    ->where('old_contratos.datainicial', date('Y-m-d'))
                                    ->where('old_contratos.situacao', '1')
                                    ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                    ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                    ->where('tipo_contrato', '<>', 'E')
                                    ->groupBy("old_contratos.id",
                                            "pessoa.nome_fantasia",
                                            "old_contratos.datainicial",
                                            "old_contratos.datafinal",
                                            "old_contratos.valor_contrato",
                                            "old_contratos.responsavel")
                            )
                            ->orderBy('Inicio')
                            ->get();
                }
                    return $faturamento_mes;
                break;
            case 'faturamento_mes':
                // return strtotime($filtroi->format('Y-m-d'));
                // return json_encode(strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01'));
                if (strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01')) {
                    $faturamento_mes = DB::table('pedido')
                                    ->select(DB::raw("pedido.id AS Contrato"),
                                            DB::raw("pessoa.nome_fantasia AS Paciente"),
                                            DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                            DB::raw("pedido.created_at AS Inicio"),
                                            DB::raw("pedido.data_validade AS Fim"),
                                            DB::raw("pedido.total AS Valor"),
                                            DB::raw("pedido.consultor AS Caixa"))
                                    ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                    ->leftjoin('pessoa AS consultor', 'pessoa.id', 'pedido.id_prof_exa')
                                    ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                    ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                    ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                    ->whereRaw("((profissional.gera_faturamento = 'S' 
                                                    or profissional.d_naofaturar >= pedido.data) OR
                                                    profissional.id is null)")
                                    ->where('pedido.status', 'F')
                                    ->whereRaw('((pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND (pedido.total is not null and pedido.total > 0))')
                                    ->where('pedido.lixeira', 0)
                                    ->where('pedido.id_emp', getEmpresa())
                                    ->where('pedido.data', '>=', $filtroi->format('Y-m-d'))
                                    ->where('pedido.data', '<=', $filtrof->format('Y-m-d'))
                                    // ->where('pedido.id', 2048)
                                    ->groupBy("pedido.id",
                                            "pessoa.nome_fantasia",
                                            "pedido.data",
                                            "pedido.data_validade",
                                            "pedido.total")
                                    ->unionAll(
                                        DB::table("old_contratos")
                                            ->select("old_contratos.id AS Contrato",
                                                        "pessoa.nome_fantasia AS Paciente",
                                                        DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                        "old_contratos.datainicial AS Inicio",
                                                        "old_contratos.datafinal AS Fim",
                                                        "old_contratos.valor_contrato AS Valor",
                                                        "old_contratos.responsavel AS Caixa")
                                            ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                            ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                            ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                            ->where('old_contratos.datainicial', '>=', $filtroi->format('Y-m-d'))
                                            ->where('old_contratos.datainicial', '<=', $filtrof->format('Y-m-d'))
                                            ->where('old_contratos.situacao', '1')
                                            ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                            ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                            ->where('tipo_contrato', '<>', 'E')
                                            ->where('old_contratos.id_emp', getEmpresa())
                                            ->groupBy("old_contratos.id",
                                                    "pessoa.nome_fantasia",
                                                    "old_contratos.datainicial",
                                                    "old_contratos.datafinal",
                                                    "old_contratos.valor_contrato",
                                                    "old_contratos.responsavel")
                                    )
                                    ->orderBy('Inicio')
                                    ->get();

                    foreach($faturamento_mes AS $faturamento) {
                        $aux_agendamentos = DB::select(DB::raw(
                            "SELECT 
                                pedido_planos.valor,
                                tabela_precos.descr
                            FROM
                                agenda
                                inner join pedido_planos on pedido_planos.id_pedido = agenda.id_pedido
                                                        and pedido_planos.id_plano  = agenda.id_tabela_preco
                                inner join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                                inner join pessoa        on pessoa.id               = agenda.id_profissional
                            WHERE
                                pedido_planos.id_pedido = ". $faturamento->Contrato . " AND 
                                agenda.lixeira = 0                                      AND 
                                agenda.status = 'F'                                     AND 
                                pessoa.gera_faturamento = 'N'" 
                        ));
                        if (sizeof($aux_agendamentos) > 0) {
                            $faturamento->Plano = str_replace($aux_agendamentos[0]->descr.",",'', $faturamento->Plano);
                            $faturamento->Valor -= $aux_agendamentos[0]->valor;
                        }
                    }
                }
                else {
                    return DB::table('pedido')
                            ->select(DB::raw("pedido.id AS Contrato"),
                                    DB::raw("pessoa.nome_fantasia AS Paciente"),
                                    DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                    DB::raw("pedido.created_at AS Inicio"),
                                    DB::raw("pedido.data_validade AS Fim"),
                                    DB::raw("pedido.total AS Valor"),
                                    DB::raw("pedido.consultor AS Caixa"))
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                            ->join('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                            ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                            ->whereRaw("((profissional.gera_faturamento = 'S' 
                                        or profissional.d_naofaturar >= pedido.data) OR
                                        profissional.id is null)")
                            ->where('pedido.data', '>=', $filtroi->format('Y-m-d'))
                            ->where('pedido.data', '<=', $filtrof->format('Y-m-d'))
                            ->where('pedido.status', 'F')
                            ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null)')
                            ->where('pedido.lixeira', 0)
                            ->where('pedido.id_emp', getEmpresa())
                            ->groupBy("pedido.id",
                                    "pessoa.nome_fantasia",
                                    "pedido.data",
                                    "pedido.data_validade",
                                    "pedido.total")
                            ->unionAll(
                                DB::table("old_contratos")
                                    ->select("old_contratos.id AS Contrato",
                                                "pessoa.nome_fantasia AS Paciente",
                                                DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                "old_contratos.datainicial AS Inicio",
                                                "old_contratos.datafinal AS Fim",
                                                "old_contratos.valor_contrato AS Valor",
                                                "old_contratos.responsavel AS Caixa")
                                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                    ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                    ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                    ->where('old_contratos.datainicial', '>=', $filtroi->format('Y-m-d'))
                                    ->where('old_contratos.datainicial', '<=', $filtrof->format('Y-m-d'))
                                    ->where('old_contratos.situacao', '1')
                                    ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                    ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                    ->where('tipo_contrato', '<>', 'E')
                                    ->groupBy("old_contratos.id",
                                            "pessoa.nome_fantasia",
                                            "old_contratos.datainicial",
                                            "old_contratos.datafinal",
                                            "old_contratos.valor_contrato",
                                            "old_contratos.responsavel")
                            )
                            ->orderBy('Inicio')
                            ->get();
                }
                    return $faturamento_mes;
                break;
            case 'faturamento_semestre':
                // return strtotime($filtroi->format('Y-m-d'));
                // return json_encode(strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01'));
                if (strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01')) {
                    $faturamento_mes = DB::table('pedido')
                                    ->select(DB::raw("pedido.id AS Contrato"),
                                            DB::raw("pessoa.nome_fantasia AS Paciente"),
                                            DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                            DB::raw("pedido.created_at AS Inicio"),
                                            DB::raw("pedido.data_validade AS Fim"),
                                            DB::raw("pedido.total AS Valor"),
                                            DB::raw("pedido.consultor AS Caixa"))
                                    ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                    ->leftjoin('pessoa AS consultor', 'pessoa.id', 'pedido.id_prof_exa')
                                    ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                    ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                    ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                    ->whereRaw("((profissional.gera_faturamento = 'S' 
                                                    or profissional.d_naofaturar >= pedido.data) OR
                                                    profissional.id is null)")
                                    ->where('pedido.status', 'F')
                                    ->whereRaw('((pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND (pedido.total is not null and pedido.total > 0))')
                                    ->where('pedido.lixeira', 0)
                                    ->where('pedido.id_emp', getEmpresa())
                                    ->whereRaw(
                                        "pedido.data >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')) ."' AND
                                        pedido.data <= '". $filtrof->format('Y-m-d') . "'"
                                    )
                                    // ->where('pedido.id', 2048)
                                    ->groupBy("pedido.id",
                                            "pessoa.nome_fantasia",
                                            "pedido.data",
                                            "pedido.data_validade",
                                            "pedido.total")
                                    ->unionAll(
                                        DB::table("old_contratos")
                                            ->select("old_contratos.id AS Contrato",
                                                        "pessoa.nome_fantasia AS Paciente",
                                                        DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                        "old_contratos.datainicial AS Inicio",
                                                        "old_contratos.datafinal AS Fim",
                                                        "old_contratos.valor_contrato AS Valor",
                                                        "old_contratos.responsavel AS Caixa")
                                            ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                            ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                            ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                            ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                            ->whereRaw(
                                                "old_contratos.datainicial >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')) ."' AND
                                                old_contratos.datainicial <= '". $filtrof->format('Y-m-d') ."'"
                                            )
                                            ->where('old_contratos.situacao', '1')
                                            ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                            ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                            ->where('tipo_contrato', '<>', 'E')
                                            ->where('old_contratos.id_emp', getEmpresa())
                                            ->groupBy("old_contratos.id",
                                                    "pessoa.nome_fantasia",
                                                    "old_contratos.datainicial",
                                                    "old_contratos.datafinal",
                                                    "old_contratos.valor_contrato",
                                                    "old_contratos.responsavel")
                                    )
                                    ->orderBy('Inicio')
                                    ->get();

                    foreach($faturamento_mes AS $faturamento) {
                        $aux_agendamentos = DB::select(DB::raw(
                            "SELECT 
                                pedido_planos.valor,
                                tabela_precos.descr
                            FROM
                                agenda
                                inner join pedido_planos on pedido_planos.id_pedido = agenda.id_pedido
                                                        and pedido_planos.id_plano  = agenda.id_tabela_preco
                                inner join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                                inner join pessoa        on pessoa.id               = agenda.id_profissional
                            WHERE
                                pedido_planos.id_pedido = ". $faturamento->Contrato . " AND 
                                agenda.lixeira = 0                                      AND 
                                agenda.status = 'F'                                     AND 
                                pessoa.gera_faturamento = 'N'" 
                        ));
                        if (sizeof($aux_agendamentos) > 0) {
                            $faturamento->Plano = str_replace($aux_agendamentos[0]->descr.",",'', $faturamento->Plano);
                            $faturamento->Valor -= $aux_agendamentos[0]->valor;
                        }
                    }
                }
                else {
                    return DB::table('pedido')
                            ->select(DB::raw("pedido.id AS Contrato"),
                                    DB::raw("pessoa.nome_fantasia AS Paciente"),
                                    DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                    DB::raw("pedido.created_at AS Inicio"),
                                    DB::raw("pedido.data_validade AS Fim"),
                                    DB::raw("pedido.total AS Valor"),
                                    DB::raw("pedido.consultor AS Caixa"))
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                            ->join('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                            ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                            ->whereRaw("((profissional.gera_faturamento = 'S' 
                                        or profissional.d_naofaturar >= pedido.data) OR
                                        profissional.id is null)")
                            ->where('pedido.data', '>=', $filtroi->format('Y-m-d'))
                            ->where('pedido.data', '<=', $filtrof->format('Y-m-d'))
                            ->where('pedido.status', 'F')
                            ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null)')
                            ->where('pedido.lixeira', 0)
                            ->where('pedido.id_emp', getEmpresa())
                            ->groupBy("pedido.id",
                                    "pessoa.nome_fantasia",
                                    "pedido.data",
                                    "pedido.data_validade",
                                    "pedido.total")
                            ->unionAll(
                                DB::table("old_contratos")
                                    ->select("old_contratos.id AS Contrato",
                                                "pessoa.nome_fantasia AS Paciente",
                                                DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                "old_contratos.datainicial AS Inicio",
                                                "old_contratos.datafinal AS Fim",
                                                "old_contratos.valor_contrato AS Valor",
                                                "old_contratos.responsavel AS Caixa")
                                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                    ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                    ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                    ->whereRaw(
                                        "old_contratos.datainicial >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -5 months')) ."' AND
                                        old_contratos.datainicial <= '". $filtrof->format('Y-m-d') ."'"
                                    )
                                    ->where('old_contratos.situacao', '1')
                                    ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                    ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                    ->where('tipo_contrato', '<>', 'E')
                                    ->groupBy("old_contratos.id",
                                            "pessoa.nome_fantasia",
                                            "old_contratos.datainicial",
                                            "old_contratos.datafinal",
                                            "old_contratos.valor_contrato",
                                            "old_contratos.responsavel")
                            )
                            ->orderBy('Inicio')
                            ->get();
                }
                    return $faturamento_mes;
                break;
            case 'faturamento_trimestre':
                    // return strtotime($filtroi->format('Y-m-d'));
                    // return json_encode(strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01'));
                    if (strtotime($filtroi->format('Y-m-d')) > strtotime('2022-11-01')) {
                        $faturamento_mes = DB::table('pedido')
                                        ->select(DB::raw("pedido.id AS Contrato"),
                                                DB::raw("pessoa.nome_fantasia AS Paciente"),
                                                DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                                DB::raw("pedido.created_at AS Inicio"),
                                                DB::raw("pedido.data_validade AS Fim"),
                                                DB::raw("pedido.total AS Valor"),
                                                DB::raw("pedido.consultor AS Caixa"))
                                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                        ->leftjoin('pessoa AS consultor', 'pessoa.id', 'pedido.id_prof_exa')
                                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                        ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                                        ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                        ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                        ->whereRaw("((profissional.gera_faturamento = 'S' 
                                                        or profissional.d_naofaturar >= pedido.data) OR
                                                        profissional.id is null)")
                                        ->where('pedido.status', 'F')
                                        ->whereRaw('((pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null) AND (pedido.total is not null and pedido.total > 0))')
                                        ->where('pedido.lixeira', 0)
                                        ->where('pedido.id_emp', getEmpresa())
                                        ->whereRaw(
                                            "pedido.data >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -2 months')) ."' AND
                                            pedido.data <= '". $filtrof->format('Y-m-d') . "'"
                                        )
                                        // ->where('pedido.id', 2048)
                                        ->groupBy("pedido.id",
                                                "pessoa.nome_fantasia",
                                                "pedido.data",
                                                "pedido.data_validade",
                                                "pedido.total")
                                        ->unionAll(
                                            DB::table("old_contratos")
                                                ->select("old_contratos.id AS Contrato",
                                                            "pessoa.nome_fantasia AS Paciente",
                                                            DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                            "old_contratos.datainicial AS Inicio",
                                                            "old_contratos.datafinal AS Fim",
                                                            "old_contratos.valor_contrato AS Valor",
                                                            "old_contratos.responsavel AS Caixa")
                                                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                                ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                                ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                                ->whereRaw(
                                                    "old_contratos.datainicial >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -2 months')) ."' AND
                                                    old_contratos.datainicial <= '". $filtrof->format('Y-m-d') ."'"
                                                )
                                                ->where('old_contratos.situacao', '1')
                                                ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                                ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                                ->where('tipo_contrato', '<>', 'E')
                                                ->where('old_contratos.id_emp', getEmpresa())
                                                ->groupBy("old_contratos.id",
                                                        "pessoa.nome_fantasia",
                                                        "old_contratos.datainicial",
                                                        "old_contratos.datafinal",
                                                        "old_contratos.valor_contrato",
                                                        "old_contratos.responsavel")
                                        )
                                        ->orderBy('Inicio')
                                        ->get();
    
                        foreach($faturamento_mes AS $faturamento) {
                            $aux_agendamentos = DB::select(DB::raw(
                                "SELECT 
                                    pedido_planos.valor,
                                    tabela_precos.descr
                                FROM
                                    agenda
                                    inner join pedido_planos on pedido_planos.id_pedido = agenda.id_pedido
                                                            and pedido_planos.id_plano  = agenda.id_tabela_preco
                                    inner join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                                    inner join pessoa        on pessoa.id               = agenda.id_profissional
                                WHERE
                                    pedido_planos.id_pedido = ". $faturamento->Contrato . " AND 
                                    agenda.lixeira = 0                                      AND 
                                    agenda.status = 'F'                                     AND 
                                    pessoa.gera_faturamento = 'N'" 
                            ));
                            if (sizeof($aux_agendamentos) > 0) {
                                $faturamento->Plano = str_replace($aux_agendamentos[0]->descr.",",'', $faturamento->Plano);
                                $faturamento->Valor -= $aux_agendamentos[0]->valor;
                            }
                        }
                    }
                    else {
                        return DB::table('pedido')
                                ->select(DB::raw("pedido.id AS Contrato"),
                                        DB::raw("pessoa.nome_fantasia AS Paciente"),
                                        DB::raw("GROUP_CONCAT(DISTINCT tabela_precos.descr) AS Plano"),
                                        DB::raw("pedido.created_at AS Inicio"),
                                        DB::raw("pedido.data_validade AS Fim"),
                                        DB::raw("pedido.total AS Valor"),
                                        DB::raw("pedido.consultor AS Caixa"))
                                ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                ->join('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                                ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                ->leftjoin('agenda', 'agenda.id', 'pedido.id_agendamento')
                                ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                ->whereRaw("((profissional.gera_faturamento = 'S' 
                                            or profissional.d_naofaturar >= pedido.data) OR
                                            profissional.id is null)")
                                ->where('pedido.data', '>=', $filtroi->format('Y-m-d'))
                                ->where('pedido.data', '<=', $filtrof->format('Y-m-d'))
                                ->where('pedido.status', 'F')
                                ->whereRaw('(pedido_forma_pag.id_forma_pag not in (8, 11, 99, 101, 103) or pedido_forma_pag.id_forma_pag is null)')
                                ->where('pedido.lixeira', 0)
                                ->where('pedido.id_emp', getEmpresa())
                                ->groupBy("pedido.id",
                                        "pessoa.nome_fantasia",
                                        "pedido.data",
                                        "pedido.data_validade",
                                        "pedido.total")
                                ->unionAll(
                                    DB::table("old_contratos")
                                        ->select("old_contratos.id AS Contrato",
                                                    "pessoa.nome_fantasia AS Paciente",
                                                    DB::raw("GROUP_CONCAT(old_modalidades.descr) AS Plano"),
                                                    "old_contratos.datainicial AS Inicio",
                                                    "old_contratos.datafinal AS Fim",
                                                    "old_contratos.valor_contrato AS Valor",
                                                    "old_contratos.responsavel AS Caixa")
                                        ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                        ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
                                        ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                        ->join('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                        ->whereRaw(
                                            "old_contratos.datainicial >= '". date("Y-m-d", strtotime($filtroi->format('Y-m-d') . ' -2 months')) ."' AND
                                            old_contratos.datainicial <= '". $filtrof->format('Y-m-d') ."'"
                                        )
                                        ->where('old_contratos.situacao', '1')
                                        ->where('old_finanreceber.id_planopagamento', '<>', 8)
                                        ->where('old_finanreceber.id_planopagamento', '<>', 11)
                                        ->where('tipo_contrato', '<>', 'E')
                                        ->groupBy("old_contratos.id",
                                                "pessoa.nome_fantasia",
                                                "old_contratos.datainicial",
                                                "old_contratos.datafinal",
                                                "old_contratos.valor_contrato",
                                                "old_contratos.responsavel")
                                )
                                ->orderBy('Inicio')
                                ->get();
                    }
                        return $faturamento_mes;
                    break;
        }
    }

    public function alcance() {
        return json_encode(DB::select(DB::raw("
            SELECT
                nif.ct AS 'naoinformado',
                mds.ct AS 'midiassociais',
                ind.ct AS 'indicacao',
                fch.ct AS 'fachada',
                ntw.ct AS 'networkmembro'
            FROM
                (
                    SELECT
                        COUNT(id) AS ct
                    FROM
                        pessoa
                    WHERE
                        (psq = '' OR psq IS NULL) AND (cliente = 'S' OR paciente = 'S') AND lixeira = 0
                ) AS nif
                LEFT JOIN
                    (
                        SELECT
                            COUNT(id) AS ct
                        FROM
                            pessoa
                        WHERE
                            psq = 'midiasocial' AND (cliente = 'S' OR paciente = 'S') AND lixeira = 0
                    ) AS mds ON 1 = 1
                LEFT JOIN
                    (
                        SELECT
                            COUNT(id) AS ct
                        FROM
                            pessoa
                        WHERE
                            psq = 'indicacao' AND (cliente = 'S' OR paciente = 'S') AND lixeira = 0
                    ) AS ind ON 1 = 1
                LEFT JOIN
                    (
                        SELECT
                            COUNT(id) AS ct
                        FROM
                            pessoa
                        WHERE
                            psq = 'fachada' AND (cliente = 'S' OR paciente = 'S') AND lixeira = 0
                    ) AS fch ON 1 = 1
                LEFT JOIN
                    (
                        SELECT
                            COUNT(id) AS ct
                        FROM
                            pessoa
                        WHERE
                            psq = 'networkmembro' AND (cliente = 'S' OR paciente = 'S') AND lixeira = 0
                    ) AS ntw ON 1 = 1
        ")));
    }
}
