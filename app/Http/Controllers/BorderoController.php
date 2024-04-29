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

class BorderoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index($filtro) {   
        $membros = DB::select(DB::raw("
        SELECT
            pessoa.id,
            pessoa.nome_fantasia
        
        FROM pessoa
        
        LEFT JOIN empresas_profissional
            ON empresas_profissional.id_profissional = pessoa.id
        
        JOIN (
            SELECT * 
            FROM historico 
            WHERE id IN (
                SELECT id 
                FROM (
                    SELECT 
                        id_pessoa, 
                        MAX(id) AS id 
                    FROM historico 
                    GROUP BY id_pessoa
                ) AS tab
            )
        ) AS hist ON hist.id_pessoa = pessoa.id
        
        WHERE (hist.acao = '".$filtro."' OR '".$filtro."' = 'T')
          AND pessoa.lixeira = 0
        
        GROUP BY
            pessoa.id,
            pessoa.nome_fantasia

        ORDER BY pessoa.nome_fantasia 
    "));

        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view("bordero", compact('membros', 'empresas', 'filtro'));
    }
    public function imprimir($id_emp, $id_membro, $id_contrato, $dinicial, $dfinal){
        $data_ini = new \DateTime($dinicial);
        $data_fim = new \DateTime($dfinal);
        $profissional = Pessoa::find($id_membro);
        $pessoas_atendidas = sizeof(DB::table('agenda')
                            ->selectRaw('id_paciente')
                            ->where('agenda.id_profissional', $id_membro)
                            ->where('agenda.data', '>=', $data_ini)
                            ->where('agenda.data', '<=', $data_fim)
                            ->where('agenda.status', 'F')
                            ->where('agenda.lixeira', '<>', true)
                            ->where('agenda.id_emp', $id_emp)
                            ->groupBy('id_paciente')
                            ->unionAll(DB::table("old_mov_atividades")
                                        ->selectRaw('old_contratos.pessoas_id')
                                        ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                        ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                                        ->where('old_mov_atividades.id_membro', $id_membro)
                                        ->where('old_mov_atividades.data', '>=', $data_ini)
                                        ->where('old_mov_atividades.data', '<=', $data_fim)
                                        ->where('status', 'F')
                                        ->groupBy('old_contratos.pessoas_id'))
                            ->get());
        
        $agendamentos = DB::table('agenda') 
                       ->select(DB::raw("CASE WHEN (agenda.id_tipo_procedimento = 5) THEN ".
                                                    "(select 'E')".
                                           " WHEN (CONCAT(',',GROUP_CONCAT(pedido_forma_pag.id_forma_pag), ',') like '%,11,%') THEN ". 
                                                    "(select 'C')".
                                           " WHEN (CONCAT(',',GROUP_CONCAT(pedido_forma_pag.id_forma_pag), ',')like '%,100,%' || procedimento.descr LIKE '%retorno%') THEN ".
                                                    "(select 'R')".
                                           " ELSE   (select 'N') END  AS tipo_pagamento"),
                                'agenda.id_modalidade                 AS id_modalidade',
                                "agenda.id                            AS id_agendamento",
                                "agenda.id_tipo_procedimento          AS id_tipo_procedimento",
                                "procedimento.descr                   AS descr_modalidade",
                                "procedimento.id                      AS id_modalidade",
                                "agenda.id_pedido                     AS id_contrato",
                                "agenda.id_tabela_preco               AS id_plano",
                                "pessoa.nome_fantasia                 AS descr_pessoa",
                                "agenda.data                          AS data",
                                "agenda.hora                          AS hora",
                                "procedimento.tipo_de_comissao        AS tipo_de_comissao",
                                "procedimento.total_agendamentos_meta AS total_agendamentos_meta",
                                "procedimento.valor_total             AS valor_total",
                                "procedimento.id_especialidade        AS id_especialidade", DB::raw("(select 0) AS antigo"))
                        ->leftjoin('procedimento',      'procedimento.id',            'agenda.id_modalidade')
                        ->join('pessoa',            'pessoa.id',                  'agenda.id_paciente')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'agenda.id_pedido')
                        ->where('agenda.id_profissional', $id_membro)
                        ->where('agenda.data', '>=', $data_ini)
                        ->where('agenda.data', '<=', $data_fim)
                        ->where('agenda.status', 'F')
                        ->where('bordero', 1)
                        ->where('agenda.id_emp', $id_emp)
                        
                        // ->where('agenda.bordero', 1)
                        ->where('agenda.lixeira', '<>', true)
                        // ->where('agenda.id_pedido', 233)
                        ->groupBy(
                                'agenda.id_modalidade',
                                "agenda.id",
                                "agenda.id_tipo_procedimento",
                                "procedimento.descr",
                                "procedimento.id",
                                "agenda.id_pedido",
                                "agenda.id_tabela_preco",
                                "pessoa.nome_fantasia",
                                "agenda.data",
                                "agenda.hora",
                                "procedimento.tipo_de_comissao",
                                "procedimento.total_agendamentos_meta",
                                "procedimento.valor_total",
                                "procedimento.id_especialidade")
                        ->unionAll(DB::table('old_mov_atividades')
                                    ->select(DB::raw("CASE WHEN (old_contratos.tipo_contrato = 'E') THEN ".
                                                                "(select 'E')".
                                                    " WHEN (old_modalidades.descr like '%retorno%') THEN ".
                                                            "(select 'R')".
                                                    " WHEN (MAX(old_finanreceber.id_planopagamento) = 11) THEN ". 
                                                            "(select 'C')".
                                                    " ELSE   (select 'N') END        AS tipo_pagamento"),
                                            'old_modalidades.id_novo                 AS id_modalidade',
                                            DB::raw("MIN(old_mov_atividades.id)      AS id_agendamento"),
                                            "old_mov_atividades.id_tipo_procedimento AS id_tipo_procedimento",
                                            "procedimento.descr                   AS descr_modalidade",
                                            "old_modalidades.id_novo                 AS id_modalidade",
                                            "old_atividades.id_contrato              AS id_contrato",
                                            "old_atividades.id                       AS id_plano",
                                            "pessoa.nome_fantasia                    AS descr_pessoa",
                                            "old_mov_atividades.data                 AS data",
                                            "old_mov_atividades.hora                 AS hora",
                                            "procedimento.tipo_de_comissao           AS tipo_de_comissao",
                                            "old_modalidades.de5                     AS total_agendamentos_meta",
                                            "old_atividades.valor_cardapio           AS valor_total",
                                            "old_modalidades.area_modalidade         AS id_especialidade", DB::raw("(select 1) AS antigo"))
                                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                    ->leftjoin('old_contratos',  'old_contratos.id',  'old_atividades.id_contrato')
                                    ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                    ->leftjoin('procedimento', 'procedimento.id', 'old_modalidades.id_novo')
                                    ->join('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                    ->where('old_mov_atividades.id_membro', $id_membro)
                                    ->where('old_mov_atividades.data', '>=', $data_ini)
                                    ->where('old_mov_atividades.data', '<=', $data_fim)
                                    ->where('old_mov_atividades.status', 'F')
                                    // ->where('bordero', 1)
                                    ->where('old_mov_atividades.lixeira', '<>', true)
                                    ->groupBy(
                                            'old_modalidades.id_novo',
                                            "old_mov_atividades.id_tipo_procedimento",
                                            "old_modalidades.descr",
                                            "old_modalidades.id_novo",
                                            "old_atividades.id",
                                            "pessoa.nome_fantasia",
                                            "old_mov_atividades.data",
                                            "old_mov_atividades.hora",
                                            "procedimento.tipo_de_comissao",
                                            "old_modalidades.de5",
                                            "old_atividades.valor_cardapio",
                                            "old_modalidades.area_modalidade")
                        )
                       ->orderBy('data')
                       ->orderBy('hora')      
                       ->get();
        
        $ag_ar = array();
        // foreach($agendamentos AS $agendamento){
        //     array_push($ag_ar, $agendamento->id_agendamento);
        //     array_push($ag_ar, $agendamento->id_modalidade);
        // }
        // return $ag_ar;
        $aux_array = array();
        $atendimentos_confirmados = sizeof($agendamentos);
        $teste = new \StdClass;
        $teste->id_modalidade = 0;
        if (sizeof($agendamentos) == 0) $agendamentos = array($teste);
        $valores     = array();
        $percentual_de_comissao = array();
        $formas_pag  = array();
        $descontos = array();
        // return $agendamentos;
        if ($agendamentos[0]->id_modalidade != 0 && sizeof($agendamentos) > 0) {
            foreach($agendamentos AS $agendamento){
                if (!in_array($agendamento->id_agendamento, $aux_array)) {
                    array_push($aux_array, $agendamento->id_agendamento);
                    $atend_confirmados = sizeof(DB::table('agenda') 
                                        ->select("agenda.id")
                                        ->where('agenda.id_profissional', $id_membro)
                                        ->where('agenda.data', '>=', $data_ini)
                                        ->where('agenda.data', '<=', $data_fim)
                                        ->where('agenda.status', 'F')
                                        ->where('agenda.lixeira', '<>', true)
                                        ->where('agenda.id_emp', $id_emp)
                                        // ->where('agenda.id_modalidade', $agendamento->id_modalidade)
                                        ->unionAll(DB::table('old_mov_atividades')
                                                    ->select('old_mov_atividades.id')
                                                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                                    ->leftjoin('old_contratos',  'old_contratos.id',  'old_atividades.id_contrato')
                                                    ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                                    ->leftjoin('procedimento', 'procedimento.id', 'old_modalidades.id_novo')
                                                    ->join('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                                    ->where('old_mov_atividades.id_membro', $id_membro)
                                                    ->where('old_mov_atividades.data', '>=', $data_ini)
                                                    ->where('old_mov_atividades.data', '<=', $data_fim)
                                                    ->where('old_mov_atividades.status', 'F')
                                                    // ->where('old_atividades.id_modalidade', $agendamento->id_modalidade)
                                                    // ->where('bordero', 1)
                                                    ->where('old_mov_atividades.lixeira', '<>', true)
                                                    ->groupBy('old_mov_atividades.id')
                                        )  
                                        ->get());
                    // return $agendamento->id_modalidade;
                    $comissao = DB::table("comissao_exclusiva")
                                ->where('id_procedimento', $agendamento->id_modalidade)
                                ->where('de2', '<=', $atend_confirmados)
                                ->where('ate2', '>=', $atend_confirmados)
                                ->get();
                    // return $comissao;
                    if ($profissional->gera_faturamento == 'N') {
                        if ($profissional->aplicar_desconto == 'S'){
                            $desconto = 0.2;
                        }
                        else {
                            $desconto = 0;
                        }
                    }
                    else {
                        $desconto = 0.2;
                    }
                    // return Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao;
                    if ($profissional->gera_faturamento == 'N' && (Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao != 'F')) {
                        $percent = (
                            $id_membro == 28480001203 && $agendamento->data >= '2023-07-01'
                        ) ? 80 : 100;
                        if (in_array($id_membro, [28480002918, 28480001482, 28480001363])) $percent = 60;
                        switch ($agendamento->antigo){
                            case '0':
                                $valor_plano = DB::table("pedido_planos")   
                                            ->selectRaw('(pedido_planos.valor/(pedido_planos.qtd_total)) AS valor')
                                            ->where('pedido_planos.id_pedido', $agendamento->id_contrato)
                                            ->where('pedido_planos.id_plano', $agendamento->id_plano)
                                            ->value('valor');
                                $valor_plano = $valor_plano - ($valor_plano * $desconto);
                                break;
                            case '1':
                                $valor_plano = $agendamento->valor_total;
                                $valor_plano = $valor_plano - ($valor_plano * $desconto);
                                break;
                        }
                        
                        // return $forma_pag;
                        array_push($valores, (($valor_plano * $percent)/100));
                        array_push($percentual_de_comissao, $percent);
                    }
                    else if (Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao === 'F'){
                        if (sizeof($comissao) > 0) {
                            array_push($valores, $comissao[0]->valor2);
                            array_push($percentual_de_comissao, $comissao[0]->valor2);
                        }
                        else {
                            if (Procedimento::find($agendamento->id_modalidade)->id_especialidade == 12) {
                                if ($id_membro == 28480002918 || $id_membro > 28480003069) {
                                    $ja_associado = DB::select(DB::raw("
                                        SELECT 
                                            pessoa.id
                                            
                                        FROM pessoa
                                        
                                        JOIN (
                                            SELECT
                                                id,
                                                id_paciente
                                                
                                            FROM pedido
                                            
                                            WHERE lixeira = 0
                                              AND data <= '".$agendamento->data."'
                                              AND data_validade >= '".$agendamento->data."'
                                            
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
                                    if (sizeof($ja_associado)) {
                                        $val = 200;
                                    } else {
                                        $convertido = DB::select(DB::raw("
                                            SELECT 
                                                pessoa.id
                                                
                                            FROM pessoa
                                            
                                            JOIN (
                                                SELECT
                                                    id,
                                                    id_paciente
                                                    
                                                FROM pedido
                                                
                                                WHERE lixeira = 0
                                                  AND data >= '".$agendamento->data."'
                                                  AND (data <= DATE_SUB('".$agendamento->data."', INTERVAL 1 MONTH))
                                                
                                                GROUP BY 
                                                    id,
                                                    id_paciente
                                            ) AS p ON p.idpaciente = pessoa.id
                                            
                                            JOIN pedido_planos
                                                ON pedido_planos.id_pedido = p.id
                                                
                                            JOIN tabela_precos
                                                ON tabela_precos.id = pedido_planos.id_plano
                                            
                                            WHERE tabela_precos.lixeira = 0
                                              AND pessoa.lixeira = 0
                                              AND tabela_precos.associado = 'S'
                                            
                                            GROUP BY id
                                        "));
                                        $val = sizeof($convertido) ? 400 : 200;
                                    }
                                } else $val = Procedimento::find($agendamento->id_modalidade)->valor_total;
                            } else $val = Procedimento::find($agendamento->id_modalidade)->valor_total;
                            array_push($valores, $val);
                            array_push($percentual_de_comissao, $val);
                        }
                    }
                    else if (Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao === '%'){
                        if ($id_membro != 360000000) {
                            if (sizeof($comissao) > 0){
                                $percent = $comissao[0]->valor2;
                            }
                            else {
                                $percent = Procedimento::find($agendamento->id_modalidade)->valor_total;
                            }
                        }
                        else {
                            $percent = 80;
                        }
                        array_push($percentual_de_comissao, $percent);
                        switch ($agendamento->antigo){
                            case '0':
                                $valor_plano = DB::table("pedido_planos")
                                            ->selectRaw('(pedido_planos.valor/(pedido_planos.qtd_total)) AS valor')
                                            ->where('pedido_planos.id_pedido', $agendamento->id_contrato)
                                            ->where('pedido_planos.id_plano', $agendamento->id_plano)
                                            ->value('valor');
                                $valor_plano = $valor_plano - ($valor_plano * $desconto);
                                break;
                            case '1':
                                $valor_plano = $agendamento->valor_total;
                                $valor_plano = $valor_plano - ($valor_plano * $desconto);
                                break;
                        } array_push($valores, (($valor_plano * $percent)/100));
                    }
                    else {
                        array_push($valores, 0);
                        array_push($percentual_de_comissao, 0);
                    }; 
                    $forma_pag = DB::table('pedido_forma_pag')
                                    ->selectRaw("CASE WHEN (pedido.id_convenio = 0) THEN GROUP_CONCAT(DISTINCT forma_pag.descr)
                                                ELSE 'CONVENIO' END As descr")
                                    ->leftjoin('forma_pag', 'forma_pag.id', 'pedido_forma_pag.id_forma_pag')
                                    ->leftjoin('pedido', 'pedido.id', 'pedido_forma_pag.id_pedido')
                                    ->where('pedido_forma_pag.id_pedido', $agendamento->id_contrato)
                                    ->groupBy('pedido_forma_pag.id_pedido')
                                    ->value('descr');
                    array_push($formas_pag, $forma_pag);
                    array_push($descontos, $desconto);
                }
            }
        }

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

        $csv = array();
        for ($i = 0; $i < sizeof($aux_array); $i++) {
            $linha = new \stdClass;
            $linha->modalidade = $agendamentos[$i]->descr_modalidade;
            $linha->contrato = $agendamentos[$i]->id_contrato;
            $linha->associado = $agendamentos[$i]->descr_pessoa;
            if (ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) == 'Sem valor' && Procedimento::find($agendamentos[$i]->id_modalidade)->tipo_de_comissao != 'F') $linha->tipo_pagamento = "SEM VALOR";
            elseif ($agendamentos[$i]->tipo_pagamento == 'R') $linha->tipo_pagamento = "RETORNO";
            elseif ($agendamentos[$i]->id_tipo_procedimento == 5 || $agendamentos[$i]->tipo_pagamento == 'E') $linha->tipo_pagamento = "EXPERIMENTAL";
            elseif ($agendamentos[$i]->tipo_pagamento == 'C') $linha->tipo_pagamento = "CORTESIA";
            else {
                if ($agendamentos[$i]->tipo_de_comissao == '%') $linha->tipo_pagamento = "Percentual de Comissão:";
                else $linha->tipo_pagamento = "Fixo: R$";
                $linha->tipo_pagamento .= " ".$percentual_de_comissao[$i];
                if ($agendamentos[$i]->tipo_de_comissao == '%') $linha->tipo_pagamento .= "%";
            }
            $linha->forma_pagamento = ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1));
            $linha->valor = "R$ ";
            $linha->valor .= (
                ($agendamentos[$i]->tipo_pagamento == 'C') ||
                ($agendamentos[$i]->id_tipo_procedimento == 5) ||
                (
                    (
                        in_array($agendamentos[$i]->tipo_pagamento, array("E", "R", "C")) || 
                        (ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) == 'Sem valor')
                    )  && Procedimento::find($agendamentos[$i]->id_modalidade)->tipo_de_comissao != 'F'
                )
            ) ? number_format(0,2,",",".") : number_format($valores[$i],2,",",".");
            array_push($csv, $linha); 
        }
        $csvJSON = strtr(json_encode($csv), $caracteres_sem_acento);

        return view('.reports.impresso_bordero', compact('profissional',
                                                         'pessoas_atendidas',
                                                         'atendimentos_confirmados',
                                                         'data_ini',
                                                         'data_fim',
                                                         'valor_total',
                                                         'agendamentos',
                                                         'valores',
                                                         'percentual_de_comissao',
                                                         'formas_pag',
                                                         'caracteres_sem_acento',
                                                         'id_emp',
                                                         'id_membro',
                                                         'id_contrato',
                                                         'dinicial',
                                                         'dfinal',
                                                         'aux_array',
                                                         'csvJSON'));
    }

    function download_xls($id_emp, $id_membro, $id_contrato, $dinicial, $dfinal) {
        $data_ini = new \DateTime($dinicial);
        $data_fim = new \DateTime($dfinal);
        $profissional = Pessoa::find($id_membro);
        $pessoas_atendidas = sizeof(DB::table('agenda')
                            ->selectRaw('id_paciente')
                            ->where('agenda.id_profissional', $id_membro)
                            ->where('agenda.data', '>=', $data_ini)
                            ->where('agenda.data', '<=', $data_fim)
                            ->where('agenda.status', 'F')
                            ->where('agenda.lixeira', '<>', true)
                            ->where('agenda.id_emp', $id_emp)
                            ->groupBy('id_paciente')
                            ->unionAll(DB::table("old_mov_atividades")
                                        ->selectRaw('old_contratos.pessoas_id')
                                        ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                        ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                                        ->where('old_mov_atividades.id_membro', $id_membro)
                                        ->where('old_mov_atividades.data', '>=', $data_ini)
                                        ->where('old_mov_atividades.data', '<=', $data_fim)
                                        ->where('status', 'F')
                                        ->groupBy('old_contratos.pessoas_id'))
                            ->get());
        
        $agendamentos = DB::table('agenda') 
                       ->select(DB::raw("CASE WHEN (agenda.id_tipo_procedimento = 5) THEN ".
                                                    "(select 'E')".
                                           " WHEN (pedido_forma_pag.id_forma_pag = 11) THEN ". 
                                                    "(select 'C')".
                                           " WHEN (pedido_forma_pag.id_forma_pag = 100 || procedimento.descr LIKE '%retorno%') THEN ".
                                                    "(select 'R')".
                                           " ELSE   (select 'N') END  AS tipo_pagamento"),
                                'agenda.id_modalidade                 AS id_modalidade',
                                "agenda.id                            AS id_agendamento",
                                "agenda.id_tipo_procedimento          AS id_tipo_procedimento",
                                "procedimento.descr                   AS descr_modalidade",
                                "procedimento.id                      AS id_modalidade",
                                "agenda.id_pedido                     AS id_contrato",
                                "agenda.id_tabela_preco               AS id_plano",
                                "pessoa.nome_fantasia                 AS descr_pessoa",
                                "agenda.data                          AS data",
                                "agenda.hora                          AS hora",
                                "procedimento.tipo_de_comissao        AS tipo_de_comissao",
                                "procedimento.total_agendamentos_meta AS total_agendamentos_meta",
                                "procedimento.valor_total             AS valor_total",
                                "procedimento.id_especialidade        AS id_especialidade", DB::raw("(select 0) AS antigo"))
                        ->leftjoin('procedimento',      'procedimento.id',            'agenda.id_modalidade')
                        ->join('pessoa',            'pessoa.id',                  'agenda.id_paciente')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'agenda.id_pedido')
                        ->where('agenda.id_profissional', $id_membro)
                        ->where('agenda.data', '>=', $data_ini)
                        ->where('agenda.data', '<=', $data_fim)
                        ->where('agenda.status', 'F')
                        ->where('bordero', 1)
                        ->where('agenda.id_emp', $id_emp)
                        // ->where('agenda.bordero', 1)
                        ->where('agenda.lixeira', '<>', true)
                        // ->where('agenda.id_pedido', 233)
                        ->groupBy("pedido_forma_pag.id_forma_pag",
                                'agenda.id_modalidade',
                                "agenda.id",
                                "agenda.id_tipo_procedimento",
                                "procedimento.descr",
                                "procedimento.id",
                                "agenda.id_pedido",
                                "agenda.id_tabela_preco",
                                "pessoa.nome_fantasia",
                                "agenda.data",
                                "agenda.hora",
                                "procedimento.tipo_de_comissao",
                                "procedimento.total_agendamentos_meta",
                                "procedimento.valor_total",
                                "procedimento.id_especialidade")
                        ->unionAll(DB::table('old_mov_atividades')
                                    ->select(DB::raw("CASE WHEN (old_contratos.tipo_contrato = 'E') THEN ".
                                                                "(select 'E')".
                                                    " WHEN (old_modalidades.descr like '%retorno%') THEN ".
                                                            "(select 'R')".
                                                    " WHEN (MAX(old_finanreceber.id_planopagamento) = 11) THEN ". 
                                                            "(select 'C')".
                                                    " ELSE   (select 'N') END        AS tipo_pagamento"),
                                            'old_modalidades.id_novo                 AS id_modalidade',
                                            DB::raw("MIN(old_mov_atividades.id)      AS id_agendamento"),
                                            "old_mov_atividades.id_tipo_procedimento AS id_tipo_procedimento",
                                            "procedimento.descr                   AS descr_modalidade",
                                            "old_modalidades.id_novo                 AS id_modalidade",
                                            "old_atividades.id_contrato              AS id_contrato",
                                            "old_atividades.id                       AS id_plano",
                                            "pessoa.nome_fantasia                    AS descr_pessoa",
                                            "old_mov_atividades.data                 AS data",
                                            "old_mov_atividades.hora                 AS hora",
                                            "procedimento.tipo_de_comissao           AS tipo_de_comissao",
                                            "old_modalidades.de5                     AS total_agendamentos_meta",
                                            "old_atividades.valor_cardapio           AS valor_total",
                                            "old_modalidades.area_modalidade         AS id_especialidade", DB::raw("(select 1) AS antigo"))
                                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                    ->leftjoin('old_contratos',  'old_contratos.id',  'old_atividades.id_contrato')
                                    ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                    ->leftjoin('procedimento', 'procedimento.id', 'old_modalidades.id_novo')
                                    ->join('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                    ->where('old_mov_atividades.id_membro', $id_membro)
                                    ->where('old_mov_atividades.data', '>=', $data_ini)
                                    ->where('old_mov_atividades.data', '<=', $data_fim)
                                    ->where('old_mov_atividades.status', 'F')
                                    // ->where('bordero', 1)
                                    ->where('old_mov_atividades.id_emp', $id_emp)
                                    ->where('old_mov_atividades.lixeira', '<>', true)
                                    ->groupBy(
                                            'old_modalidades.id_novo',
                                            "old_mov_atividades.id_tipo_procedimento",
                                            "old_modalidades.descr",
                                            "old_modalidades.id_novo",
                                            "old_atividades.id",
                                            "pessoa.nome_fantasia",
                                            "old_mov_atividades.data",
                                            "old_mov_atividades.hora",
                                            "procedimento.tipo_de_comissao",
                                            "old_modalidades.de5",
                                            "old_atividades.valor_cardapio",
                                            "old_modalidades.area_modalidade")
                        )
                       ->orderBy('data')
                       ->orderBy('hora')      
                       ->get();
        // return $agendamentos;
        $ag_ar = array();
        // foreach($agendamentos AS $agendamento){
        //     array_push($ag_ar, $agendamento->id_agendamento);
        //     array_push($ag_ar, $agendamento->id_modalidade);
        // }
        // return $ag_ar;
        $atendimentos_confirmados = sizeof($agendamentos);
        $teste = new \StdClass;
        $teste->id_modalidade = 0;
        if (sizeof($agendamentos) == 0) $agendamentos = array($teste);
        $valores     = array();
        $percentual_de_comissao = array();
        $formas_pag  = array();
        // return $agendamentos;
        if ($agendamentos[0]->id_modalidade != 0 && sizeof($agendamentos) != 1) {
            foreach($agendamentos AS $agendamento){
                $atend_confirmados = sizeof(DB::table('agenda') 
                                    ->select("agenda.id")
                                    ->leftjoin('procedimento',      'procedimento.id',            'agenda.id_modalidade')
                                    ->leftjoin('pessoa',            'pessoa.id',                  'agenda.id_paciente')
                                    ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'agenda.id_pedido')
                                    ->where('agenda.id_profissional', $id_membro)
                                    ->where('agenda.data', '>=', $data_ini)
                                    ->where('agenda.data', '<=', $data_fim)
                                    ->where('agenda.status', 'F')
                                    ->where('agenda.lixeira', '<>', true)
                                    ->where('agenda.id_emp', $id_emp)
                                    ->groupBy("agenda.id")
                                    ->unionAll(DB::table('old_mov_atividades')
                                                ->select("old_mov_atividades.id")
                                                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                                                ->leftjoin('old_contratos',  'old_contratos.id',  'old_atividades.id_contrato')
                                                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                                                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                                ->leftjoin('procedimento', 'procedimento.id', 'old_modalidades.id_novo')
                                                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                                                ->where('old_mov_atividades.id_membro', $id_membro)
                                                ->where('old_mov_atividades.data', '>=', $data_ini)
                                                ->where('old_mov_atividades.data', '<=', $data_fim)
                                                ->where('old_mov_atividades.status', 'F')
                                                ->where('old_mov_atividades.lixeira', '<>', true)
                                                ->groupBy("old_mov_atividades.id")
                                    )   
                                    ->get());
                $comissao = DB::table("comissao_exclusiva")
                            ->where('id_procedimento', $agendamento->id_modalidade)
                            ->where('de2', '<=', $atend_confirmados)
                            ->where('ate2', '>=', $atend_confirmados)
                            ->get();
                
                if ($profissional->gera_faturamento == 'N') {
                    if ($profissional->aplicar_desconto == 'S'){
                        $desconto = 0.2;
                    }
                    else {
                        $desconto = 0;
                    }
                }
                else {
                    $desconto = 0.2;
                }
                // return Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao;
                if ($profissional->gera_faturamento == 'N') {
                    $percent = 100;
                    array_push($percentual_de_comissao, $percent);
                    switch ($agendamento->antigo){
                        case '0':
                            $valor_plano = DB::table("pedido_planos")
                                        ->selectRaw('(pedido_planos.valor/(pedido_planos.qtd_total)) AS valor')
                                        ->where('pedido_planos.id_pedido', $agendamento->id_contrato)
                                        ->where('pedido_planos.id_plano', $agendamento->id_plano)
                                        ->value('valor');
                            $valor_plano = $valor_plano - ($valor_plano * $desconto);
                            break;
                        case '1':
                            $valor_plano = $agendamento->valor_total;
                            $valor_plano = $valor_plano - ($valor_plano * $desconto);
                            break;
                    }
                    
                    // return $forma_pag;
                    array_push($valores, (($valor_plano * $percent)/100));
                    array_push($percentual_de_comissao, $percent);
                }
                else if (Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao === 'F'){
                    if (sizeof($comissao) > 0) {
                        array_push($valores, $comissao[0]->valor2);
                        array_push($percentual_de_comissao, $comissao[0]->valor2);
                    }
                    else {
                        array_push($valores, Procedimento::find($agendamento->id_modalidade)->valor_total);
                        array_push($percentual_de_comissao, Procedimento::find($agendamento->id_modalidade)->valor_total);
                    }
                }
                else if (Procedimento::find($agendamento->id_modalidade)->tipo_de_comissao === '%'){
                    if ($id_membro != 360000000) {
                        if (sizeof($comissao) > 0){
                            $percent = $comissao[0]->valor2;
                        }
                        else {
                            $percent = Procedimento::find($agendamento->id_modalidade)->valor_total;
                        }
                    }
                    else {
                        $percent = 80;
                    }
                    array_push($percentual_de_comissao, $percent);
                    switch ($agendamento->antigo){
                        case '0':
                            $valor_plano = DB::table("pedido_planos")
                                        ->selectRaw('(pedido_planos.valor/(pedido_planos.qtd_total)) AS valor')
                                        ->where('pedido_planos.id_pedido', $agendamento->id_contrato)
                                        ->where('pedido_planos.id_plano', $agendamento->id_plano)
                                        ->value('valor');
                            $valor_plano = $valor_plano - ($valor_plano * $desconto);
                            break;
                        case '1':
                            $valor_plano = $agendamento->valor_total;
                            $valor_plano = $valor_plano - ($valor_plano * $desconto);
                            break;
                    } array_push($valores, (($valor_plano * $percent)/100));
                }
                else {
                    array_push($valores, 0);
                    array_push($percentual_de_comissao, 0);
                }; 
                $forma_pag = DB::table('pedido_forma_pag')
                                 ->selectRaw("CASE WHEN (pedido.id_convenio = 0) THEN GROUP_CONCAT(DISTINCT forma_pag.descr)
                                              ELSE 'CONVENIO' END As descr")
                                 ->leftjoin('forma_pag', 'forma_pag.id', 'pedido_forma_pag.id_forma_pag')
                                 ->leftjoin('pedido', 'pedido.id', 'pedido_forma_pag.id_pedido')
                                 ->where('pedido_forma_pag.id_pedido', $agendamento->id_contrato)
                                 ->groupBy('pedido_forma_pag.id_pedido')
                                 ->value('descr');
                array_push($formas_pag, $forma_pag);
            }
        }

        $caracteres_sem_acento = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Â'=>'Z', 'Â'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Å'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'Å'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'Ä'=>'a', 'î'=>'i', 'â'=>'a', 'È'=>'s', 'È'=>'t', 'Ä'=>'A', 'Î'=>'I', 'Â'=>'A', 'È'=>'S', 'È'=>'T',
        );


 
        Excel::create('New file', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $users = App\User::all();
                $sheet->loadView('.reports.impresso_bordero2', compact('profissional',
                                                                      'pessoas_atendidas',
                                                                      'atendimentos_confirmados',
                                                                      'data_ini',
                                                                      'data_fim',
                                                                      'valor_total',
                                                                      'agendamentos',
                                                                      'valores',
                                                                      'percentual_de_comissao',
                                                                      'formas_pag',
                                                                      'caracteres_sem_acento',
                                                                      'id_emp',
                                                                      'id_membro',
                                                                      'id_contrato',
                                                                      'dinicial',
                                                                      'dfinal'));
            });
        })->download('xls');
    }



    function atualizar_modalidades(Request $request) {
        for ($i = 0; $i < sizeof($request->antigas); $i++){
            $aux = OldModalidades::find($request->antigas[$i]);
            $aux->id_novo = $request->novas[$i];
            $aux->save();
        }
        return 'true';
    }
}
