<?php

namespace App\Http\Controllers;

use DB;
use App\Procedimento;

class TransferenciaEmpresasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {   

        return view("transferencia_empresas");
    }

    private function queryAssoc($where) {
        return DB::select(DB::raw("
            SELECT 
                pessoa.id
                
            FROM pessoa
            
            JOIN (
                SELECT
                    id,
                    id_paciente
                    
                FROM pedido
                
                ".$where."
                
                GROUP BY 
                    id,
                    id_paciente
            ) AS p ON p.id_paciente = pessoa.id
            
            JOIN pedido_planos
                ON pedido_planos.id_pedido = p.id
                
            JOIN tabela_precos
                ON tabela_precos.id = pedido_planos.id_plano
            
            WHERE tabela_precos.lixeira = 0
              AND pessoa.lixeira = 0
              AND tabela_precos.associado = 'S'
            
            GROUP BY id
        "));
    }

    public function imprimir($dinicial, $dfinal, $orientacao) {
        $caracteres_sem_acento = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Â'=>'Z', 'Â'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Ã‰'=>'E',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Å'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'Å'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'Ãƒ'=>'A', 'Ä'=>'a', 'î'=>'i', 'â'=>'a', 'È'=>'s', 'È'=>'t', 'Ä'=>'A', 'Î'=>'I', 'Â'=>'A', 'È'=>'S', 'È'=>'T',
        );

        $empresa = 0;
        $data_ini = new \DateTime($dinicial);
        $data_fim = new \DateTime($dfinal);
        $data_inicial = $data_ini->format('d/m/Y');
        $data_final = $data_fim->format('d/m/Y');
        $query = "
            SELECT
                emp_origem.descr AS emp_origem,
                emp_destino.descr AS emp_destino,
                pedido.id AS contrato,
                CONCAT(DATE_FORMAT(agenda.data, '%d/%m/%Y'), ' às ', SUBSTRING(agenda.hora, 1, 5)) AS data,
                UPPER(paciente.nome_fantasia) AS associado,

                UPPER(profissional.nome_fantasia) AS membro,
                DATE_FORMAT(pedido.data, '%d/%m/%Y') AS pedido_data,
                DATE_FORMAT(pedido.data_validade, '%d/%m/%Y') AS pedido_validade,
                pedido.total AS pedido_total,

                agenda.id_profissional,
                profissional.gera_faturamento,
                procedimento.id_especialidade,
                procedimento.tipo_de_comissao,
                procedimento.valor_total,
                pp.valor AS valor_plano,
                pfp.descr AS descr_pfp,
                agenda.data AS sdata,
                CASE
                    WHEN (agenda.id_tipo_procedimento = 5) THEN 'E'
                    WHEN (CONCAT(',', pfp.lista, ',') LIKE '%,11,%') THEN 'C'
                    WHEN ((CONCAT(',', pfp.lista, ',') LIKE '%,100,%') OR (procedimento.descr LIKE '%retorno%')) THEN 'R'
                    ELSE 'N'
                END AS tipo_pagamento,
                CASE
                    WHEN (atendimentos.ct BETWEEN comissao_exclusiva.de2 AND comissao_exclusiva.ate2) THEN comissao_exclusiva.valor2
                    ELSE 0
                END AS comissao,
                CASE
                    WHEN (profissional.aplicar_desconto <> 'S' AND profissional.gera_faturamento = 'N') THEN 0
                    ELSE 0.2
                END AS desconto
            
            FROM agenda
            
            JOIN pedido
                ON pedido.id = agenda.id_pedido
            
            JOIN empresa AS emp_origem
                ON emp_origem.id = pedido.id_emp

            JOIN empresa AS emp_destino
                ON emp_destino.id = agenda.id_emp
            
            JOIN pessoa AS paciente
                ON paciente.id = agenda.id_paciente
                
            JOIN pessoa AS profissional
                ON profissional.id = agenda.id_profissional
                
            JOIN procedimento
                ON procedimento.id = agenda.id_modalidade
                
            LEFT JOIN comissao_exclusiva
                ON comissao_exclusiva.id_procedimento = procedimento.id
            
            JOIN (
                SELECT
                    id_pedido,
                    id_plano,
                    (pedido_planos.valor / (pedido_planos.qtd_total)) AS valor
                
                FROM pedido_planos
            ) AS pp ON pp.id_pedido = agenda.id_pedido AND pp.id_plano = agenda.id_tabela_preco
                
            JOIN (
                SELECT
                    id_pedido,
                    GROUP_CONCAT(id_forma_pag) AS lista,
                    CASE
                        WHEN (pedido.id_convenio = 0) THEN GROUP_CONCAT(DISTINCT forma_pag.descr)
                        ELSE 'CONVENIO'
                    END AS descr
                
                FROM pedido_forma_pag
                
                LEFT JOIN forma_pag
                    ON forma_pag.id = pedido_forma_pag.id_forma_pag
                    
                JOIN pedido
                    ON pedido.id = pedido_forma_pag.id_pedido
                
                GROUP BY id_pedido
            ) AS pfp ON pfp.id_pedido = pedido.id
            
            JOIN (
                SELECT
                    id_profissional,
                    id_emp,
                    COUNT(id) AS ct
                
                FROM agenda
                
                WHERE lixeira = 0
                  AND status = 'F'
                  AND data BETWEEN '".$dinicial."' AND '".$dfinal."'
                
                GROUP BY
                    id_profissional,
                    id_emp
            ) AS atendimentos ON atendimentos.id_profissional = agenda.id_profissional AND atendimentos.id_emp = agenda.id_emp
                
            WHERE pedido.id_emp <> agenda.id_emp
              AND pedido.lixeira = 0
              AND agenda.lixeira = 0
              AND agenda.status = 'F'
              AND agenda.bordero = 1
              AND agenda.data BETWEEN '".$dinicial."' AND '".$dfinal."'
              AND (agenda.id_emp = ".$empresa." OR ".$empresa." = 0)
            
            ORDER BY
                pedido.id_emp,
                agenda.id_emp,
                pedido.id,
                agenda.data
        ";
        //return $query;
        $consulta = DB::select(DB::raw($query));
        $resultado = array();        
        foreach($consulta as $linha) {
            if (!(
                in_array($linha->tipo_pagamento, array('C', 'E')) ||
                (
                    (
                        in_array($linha->tipo_pagamento, array("E", "R", "C")) || 
                        (ucfirst(substr($linha->descr_pfp, 0, 1)) . strtolower(substr(strtr($linha->descr_pfp, $caracteres_sem_acento), 1)) == 'Sem valor')
                    )  && $linha->$linha->tipo_de_comissao != 'F'
                )
            )) {
                if ($linha->gera_faturamento == 'N' && $linha->tipo_de_comissao != 'F') {
                    $percent = (
                        $linha->id_profissional == 28480001203 && $linha->sdata >= '2023-07-01'
                    ) ? 80 : 100;
                    $valor_plano = $linha->valor_plano - ($linha->valor_plano * $linha->desconto);
                    $valor = ($valor_plano * $percent) / 100;
                } else if ($linha->tipo_de_comissao == '%') {
                    $percent = $linha->id_profissional != 360000000 ?
                        $linha->comissao > 0 ? $linha->comissao : $linha->valor_total
                    : 80;
                    $valor = ($linha->valor_plano * $percent) / 100;
                } else if ($linha->tipo_de_comissao == 'F') {
                    if ($linha->id_especialidade == 12 && ($linha->id_profissional == 28480002918 || $linha->id_profissional > 28480003069)) {
                        $ja_associado = $this->queryAssoc("
                            WHERE lixeira = 0
                              AND data <= '".$linha->sdata."'
                              AND data_validade >= '".$linha->sdata."'
                        ");
                        if (sizeof($ja_associado)) $valor = 200;
                        else {
                            $convertido = $this->queryAssoc("
                                WHERE lixeira = 0
                                  AND data >= '".$linha->sdata."'
                                  AND (data <= DATE_SUB('".$linha->sdata."', INTERVAL 1 MONTH))
                            ");
                            $valor = sizeof($convertido) ? 400 : 200;
                        }
                    } else $valor = $linha->comissao > 0 ? $linha->comissao : $linha->valor_total;
                } else $valor = 0;
            } else $valor = 0;
            $linha_final = new \stdClass;
            // GRUPO 1
            $linha_final->emp_origem = $linha->emp_origem;

            // GRUPO 2
            $linha_final->emp_destino = $linha->emp_destino;

            // GRUPO 3
            $linha_final->contrato = $linha->contrato;
            $linha_final->pedido_data = $linha->pedido_data;
            $linha_final->pedido_validade = $linha->pedido_validade;
            $linha_final->associado = $linha->associado;
            $linha_final->pedido_total = $linha->pedido_total;
            
            // DETALHES
            $linha_final->data = $linha->data;
            $linha_final->membro = $linha->membro;
            $linha_final->valor = $valor;
            array_push($resultado, $linha_final);
        }
        $csvJSON = strtr(json_encode($resultado), $caracteres_sem_acento);
        $resultado = collect($resultado)->groupBy('emp_origem')->map(function($items1) {
            return [
                'origem' => [
                    'descr' => $items1[0]->emp_origem,
                    'soma' => $items1->sum('valor'),
                    'destino' => collect($items1)->groupBy('emp_destino')->map(function($items2) {
                        return [
                            'descr' => $items2[0]->emp_destino,
                            'soma' => $items2->sum('valor'),
                            'contratos' => collect($items2)->groupBy('contrato')->map(function($items3) {
                                return [
                                    'id' => $items3[0]->contrato,
                                    'pedido_data' => $items3[0]->pedido_data,
                                    'pedido_validade' => $items3[0]->pedido_validade,
                                    'associado' => $items3[0]->associado,
                                    'pedido_total' => $items3[0]->pedido_total,
                                    'soma' => $items3->sum('valor'),
                                    'agendamentos' => $items3->map(function($agendamento) {
                                        return [
                                            'data' => $agendamento->data,
                                            'membro' => $agendamento->membro,
                                            'valor' => $agendamento->valor
                                        ];
                                    })->values()->all()
                                ];
                            })->values()->all()
                        ];
                    })->values()->all()
                ]
            ];
        })->values()->all();
        //return $resultado;
        return view('.reports.impresso_transferencia_empresas', compact('data_inicial', 'data_final', 'resultado', 'csvJSON'));
    }

    public function imprimir_old($dinicial, $dfinal, $orientacao){
        $datainicial = new \Datetime($dinicial);
        $datafinal = new \DateTime($dfinal);

        $data_inicial = $datainicial->format('d/m/Y');
        $data_final = $datafinal->format('d/m/Y');

        $data = new \StdClass;

        $empresas = DB::select(
                        DB::raw(
                            "SELECT
                                empresa.id,
                                empresa.descr
                            FROM
                                empresa"
                            )
                        );
        foreach($empresas AS $empresa){

            $empresa->empresas_divergentes  = DB::select(
                                                    DB::raw(
                                                        "SELECT
                                                            GROUP_CONCAT(distinct pedido.id) as ids,
                                                            empresa.id AS id,
                                                            empresa.descr AS descr
                                                        FROM
                                                            agenda
                                                            inner join pedido on pedido.id = agenda.id_pedido
                                                            left join empresa on empresa.id = agenda.id_emp
                                                        WHERE
                                                            pedido.id_emp = ". $empresa->id ." AND 
                                                            agenda.id_emp <> pedido.id_emp     AND
                                                            agenda.lixeira = 0                 AND
                                                            agenda.status = 'F'                AND
                                                            agenda.data >= '". $dinicial. "'   AND
                                                            agenda.data <= '". $dfinal  . "' 
                                                        GROUP BY
                                                            empresa.id,
                                                            empresa.descr"
                                                        )
                                                    );
            foreach($empresa->empresas_divergentes as $empresa_divergente){
                $empresa_divergente->contratos = DB::select(
                                        DB::raw(
                                            "SELECT
                                                pedido.id,
                                                DATE_FORMAT(pedido.data, '%d/%m/%Y') AS data,
                                                DATE_FORMAT(pedido.data_validade, '%d/%m/%Y') as data_validade,
                                                GROUP_CONCAT(distinct tabela_precos.descr) as planos,
                                                pedido.total,
                                                pessoa.nome_fantasia
                                            FROM
                                                agenda
                                                left join pedido on pedido.id = agenda.id_pedido
                                                left join pessoa on pessoa.id = pedido.id_paciente
                                                left join pedido_planos on pedido_planos.id_pedido = pedido.id AND
                                                                           pedido_planos.id_plano  = agenda.id_tabela_preco
                                                left join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                                            WHERE
                                                agenda.id_emp = ". $empresa_divergente->id ." AND 
                                                pedido.id_emp = ". $empresa->id ."            AND
                                                agenda.lixeira = 0                 AND
                                                agenda.status = 'F'                AND
                                                agenda.data >= '". $dinicial. "'   AND
                                                agenda.data <= '". $dfinal  . "' 
                                            GROUP BY
                                                pedido.id,
                                                pedido.data,
                                                pedido.data_validade,
                                                pedido.total,
                                                pessoa.nome_fantasia
                                            ORDER BY
                                                pedido.id_emp"
                                            )
                                        );

                
                
                $contratos = DB::select(
                    DB::raw(
                        "SELECT
                            pedido.id,
                            DATE_FORMAT(pedido.data, '%d/%m/%Y') AS data,
                            DATE_FORMAT(pedido.data_validade, '%d/%m/%Y') as data_validade,
                            GROUP_CONCAT(distinct tabela_precos.descr) as planos,
                            pedido.total,
                            pessoa.nome_fantasia
                        FROM
                            agenda
                            left join pedido on pedido.id = agenda.id_pedido
                            left join pessoa on pessoa.id = pedido.id_paciente
                            left join pedido_planos on pedido_planos.id_pedido = pedido.id AND
                                                       pedido_planos.id_plano  = agenda.id_tabela_preco
                            left join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                        WHERE
                            agenda.id_emp = ". $empresa->id ." AND 
                            pedido.id_emp = ". $empresa_divergente->id ." AND
                            agenda.lixeira = 0                 AND
                            agenda.status = 'F'                AND
                            agenda.data >= '". $dinicial. "'   AND
                            agenda.data <= '". $dfinal  . "' 
                        GROUP BY
                            pedido.id,
                            pedido.data,
                            pedido.data_validade,
                            pedido.total,
                            pessoa.nome_fantasia
                        ORDER BY
                            pedido.id_emp"
                        )
                    );
                
                

                foreach($empresa_divergente->contratos AS $contrato) {
                    $contrato->agendamentos = DB::select(
                                                DB::raw(
                                                    "SELECT
                                                        DATE_FORMAT(agenda.data, '%d/%m/%Y') as data,
                                                        SUBSTRING(agenda.hora, 1, 5) as hora,
                                                        pessoa.nome_fantasia,
                                                        tabela_precos.descr,
                                                        (pedido_planos.valor/(pedido_planos.qtd_original*pedido_planos.qtde)) as total
                                                    FROM
                                                        agenda
                                                        left join pessoa on pessoa.id = agenda.id_profissional
                                                        left join pedido on pedido.id = agenda.id_pedido
                                                        left join pedido_planos on pedido_planos.id_pedido = agenda.id_pedido AND
                                                                                pedido_planos.id_plano  = agenda.id_tabela_preco
                                                        left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
                                                    WHERE
                                                        agenda.id_pedido = ". $contrato->id ." AND
                                                        agenda.data >= '". $data_inicial . "'  AND
                                                        agenda.data <= '". $data_final   . "'  AND
                                                        agenda.id_emp <> pedido.id_emp         AND
                                                        agenda.lixeira = 0                 AND
                                                        agenda.status = 'F'                AND
                                                        agenda.data >= '". $dinicial. "'   AND
                                                        agenda.data <= '". $dfinal  . "' 
                                                        "
                                                )
                                                );
                    $contrato->total_convertido = 0;
                    foreach($contrato->agendamentos AS $agendamento){
                        $contrato->total_convertido += $agendamento->total;
                    }



                    $empresa_divergente->total_transferencia = 0;
                    foreach($contrato->agendamentos as $contrato){
                        $empresa_divergente->total_transferencia += $contrato->total;
                    }
                    $empresa_divergente->total_a_transferir = 0 - $empresa_divergente->total_transferencia;
                
                    foreach($contratos as $contrato) {
                        $empresa_divergente->total_a_transferir += $contrato->total;
                    }
                    if ($empresa_divergente->total_a_transferir < 0){
                        $empresa_divergente->total_a_transferir = 0;
                    }
                }
            }
        }
        // return json_encode($empresas);

        return view('.reports.impresso_transferencia_empresas', compact(
                                                                    'data_inicial',
                                                                    'data_final',
                                                                    'empresas' 
        ));
        
    }

}
