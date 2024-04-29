<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Pessoa;
use App\AnamnesePessoa;
use App\ZEnvia;
use App\DadosApp;
use App\IECPessoa;
use App\Agenda;
use App\Anexos;
use App\UsersApp;
use App\IECQuestao;
use App\GradeHorario;
use App\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppApiController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
    public function login(Request $request)
    {
        $login = DB::table('usersApp')
            ->where('email', $request->email)
            ->first();


        $data = new \StdClass;
        if ($login) {
            if ($login->senha === $request->senha) {
                $data->external_id = $login->id_pessoa;
                $data->external_name = Pessoa::find($login->id_pessoa)->nome_fantasia;
                if (file_exists(public_path("img/pessoa/") . $login->id_pessoa . ".jpg")) {
                    $data->external_avatar = "http://vps.targetclient.com.br/saude-beta/img/pessoa/" . $login->id_pessoa . ".jpg";
                } else {
                    $url_img = 'http://vps.targetclient.com.br/saude-beta/img/';

                    if (Pessoa::find($login->id_pessoa)->sexo == 'M') {
                        $url_img .= 'avatar-m.png';
                    } else if (Pessoa::find($login->id_pessoa)->sexo == 'F') {
                        $url_img .= 'avatar-f.png';
                    } else {
                        $url_img .= 'avatar-ss.png';
                    }
                    $data->external_avatar = $url_img;
                }
                $data->pass = true;
            } else {
                $data->pass = false;
                $data->error = "Senha Incorreta";
            }
        } else {
            $data->error = "Usuário não encontrato";
            $data->pass = false;
        }
        return json_encode($data);
    }
    public function getCalendar(Request $request)
    {
        // return $request->stCalendarFilter['iClientID'];

        $agendamento = DB::table('agenda')
            ->select(
                'agenda.id AS iAtividadeID',
                DB::raw("CASE WHEN (pedido.tipo_contrato = 'P') THEN 'PRÉ AGENDAMENTO'
                                   WHEN (pedido.tipo_contrato = 'N') THEN 'CONTRATO'
                                   ELSE 'EXPERIMENTAL' END AS sContrato"),
                'agenda_status.descr As sStatus',
                'procedimento.descr AS sModalidade',
                'profissional.nome_fantasia AS sMembro',
                DB::raw("agenda.data AS sData"),
                'agenda.hora As sInicio',
                DB::raw("(select '') AS sFim"),
                DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', agenda.id_modalidade, '.png') AS sImg"),
                DB::raw("DATE_FORMAT(agenda.data, '%m/%Y') as sMesAno"),
                'agenda.obs As sSobre',
                DB::raw("(select 1) as bQuebra"),
                'agenda.travar as confirmado',
                'agenda.id_confirmacao'
            )
            ->leftjoin("agenda_status", "agenda_status.id", "agenda.id_status")
            ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
            ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
            ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
            ->where('agenda.id_paciente', $request->stCalendarFilter['iClientID'])
            ->where('agenda.data', '>=', $request->stCalendarFilter['sInitialDate'])
            ->where('agenda.data', '<=', $request->stCalendarFilter['sFinalDate'])
            ->where('agenda.lixeira', 0)
            ->where('agenda.status', '<>', 'C')
            ->unionAll(
                DB::table('old_mov_atividades')
                    ->selectRaw("old_mov_atividades.id AS iAtividadeID, "
                        . "       CASE WHEN (old_contratos.tipo_contrato = 'E')                               THEN 'EXPERIMENTAL'"
                        . "            WHEN (old_contratos.id_plano = 0 AND old_contratos.valor_contrato = 0) THEN 'PRE-AGENDAMENTO'"
                        . "            ELSE old_contratos.id END AS sContrato,"
                        . "       CASE WHEN old_mov_atividades.status = 'F' THEN 'FINALIZADO'"
                        . "            WHEN old_mov_atividades.status = 'C' THEN 'CANCELADO'"
                        . "            ELSE 'ABERTO' END AS sStatus,"
                        . "       old_modalidades.descr AS sModalidade, 
                                 membros.nome_fantasia AS sMembro, 
                                 old_mov_atividades.data sData, 
                                 old_mov_atividades.hora AS sInicio, 
                                 (select '') AS sFim, 
                                 CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', old_atividades.id_modalidade, '.png') AS sImg,
                                 DATE_FORMAT(old_mov_atividades.data,'%m/%Y') AS sMesAno,
                                 (select '') AS sSobre,
                                 (select 1) AS bQuebra,
                                 0 as confirmado,
                                 0 as id_confirmacao")
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('pessoa AS membros', 'membros.id', 'old_mov_atividades.id_membro')
                    ->where('old_contratos.pessoas_id', $request->stCalendarFilter['iClientID'])
                    ->where('old_mov_atividades.data', '>=', $request->stCalendarFilter['sInitialDate'])
                    ->where('old_mov_atividades.data', '<=', $request->stCalendarFilter['sFinalDate'])
            )
            ->orderBy('sdata')
            ->orderBy('sInicio')
            ->get();
        $resultado = array();
        foreach($agendamento as $linha) {
            $dt = new \DateTime($linha->sData);
            $linha->sData = $dt->format('d/m/Y');
            array_push($resultado, $linha);
        }
        $mes_old = "";
        for ($i = 0; $i < sizeof($agendamento); $i++) {
            if (substr($agendamento[$i]->sData, 3, 2) == $mes_old)
                $agendamento[$i]->bQuebra = 0;
            else {
                $mes_old = substr($agendamento[$i]->sData, 3, 2);
                $agendamento[$i]->bQuebra = 1;
            }
        }
        if (sizeof($agendamento) > 0)
            $agendamento[0]->bQuebra = 1;
        

        return json_encode($resultado);

    }

    public function getCalendarDetails(Request $request)
    {
        // return $request->iId;
        if ($request->iId > 1365811) {
            return json_encode(
                DB::table('agenda')
                    ->select(
                        'agenda.id AS iAtividadeID',
                        DB::raw("CONCAT('Contratado em: ', pedido.data) AS sContrato"),
                        'agenda_status.descr As sStatus',
                        'procedimento.descr AS sModalidade',
                        'profissional.nome_fantasia AS sMembro',
                        DB::raw("DATE_FORMAT(agenda.data, '%d/%m/%Y') AS sData"),
                        'agenda.hora As sInicio',
                        DB::raw("(select '') AS sFim"),
                        DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', agenda.id_modalidade, '.png') AS sImg"),
                        DB::raw("DATE_FORMAT(agenda.data, '%m/%Y') as sMesAno"),
                        'agenda.obs As sSobre',
                        DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/pessoa/', agenda.id_modalidade, '.png') AS sImg"),
                        DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/pessoa/', agenda.id_profissional, '.jpg') AS sFotoMembro"),
                        DB::raw("
                        CASE WHEN (agenda.id_confirmacao = 1) THEN 'Presença Confirmada'
                             WHEN (agenda.id_confirmacao = 2) THEN 'Ausencia Confirmada'
                             ELSE 'Não Informado' END as confirmacao
                        ")
                    )
                    ->leftjoin("agenda_status", "agenda_status.id", "agenda.id_status")
                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                    ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                    ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                    ->where('agenda.id', $request->iId)
                    ->get()
            );
        } else {
            return json_encode(
                DB::table('old_mov_atividades')
                    ->selectRaw("old_mov_atividades.id AS iAtividadeID, "
                        . "       CASE WHEN (old_contratos.tipo_contrato = 'E')                               THEN 'EXPERIMENTAL'"
                        . "            WHEN (old_contratos.id_plano = 0 AND old_contratos.valor_contrato = 0) THEN 'PRE-AGENDAMENTO'"
                        . "            ELSE old_contratos.id END AS sContrato,"
                        . "       CASE WHEN old_mov_atividades.status = 'F' THEN 'FINALIZADO'"
                        . "            WHEN old_mov_atividades.status = 'C' THEN 'CANCELADO'"
                        . "            ELSE 'ABERTO' END AS sStatus,"
                        . "       old_modalidades.descr AS sModalidade, 
                                 membros.nome_fantasia AS sMembro, 
                                 old_mov_atividades.data sData, 
                                 old_mov_atividades.hora AS sInicio, 
                                 (select '') AS sFim, 
                                 CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', old_atividades.id_modalidade, '.png') AS sImg,
                                 DATE_FORMAT(old_mov_atividades.data,'%m/%Y') AS sMesAno,
                                 (select '') AS sSobre,
                                 (select 1) AS bQuebra,
                                 CONCAT('http://vps.targetclient.com.br/saude-beta/img/', membros.id_emp, '/', membros.id, '.jpg') AS sFotoMembro,
                                 CASE WHEN (old_atividades.id_confirmacao = 1) THEN 'Presença Confirmada'
                                      WHEN (old_atividades.id_confirmacao = 2) THEN 'Ausencia Confirmada'
                                      ELSE 'Não Informado' END as confirmacao")
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('pessoa AS membros', 'membros.id', 'old_mov_atividades.id_membro')
                    ->where('old_mov_atividades.id', $request->iId)
                    ->get()
            );
        }

    }
    public function saveHealth(Request $request)
    {
        foreach ($request->stHealthData as $dados_app) {
            $dinicial = new \DateTime($dados_app['startDate']);
            $dfinal = new \DateTime($dados_app['endDate']);

            $aux = DB::table('dados_app')
                ->where('id_paciente', $dados_app['person_id'])
                ->where('tipo_dados', $dados_app['type_id'])
                ->where('inicial', $dinicial->format('Y-m-d H:i:s'))
                ->where('final', $dfinal->format('Y-m-d H:i:s'))
                //    ->where('data',  $dinicial->format('Y-m-d'))
                ->where('valor', $dados_app['value'])
                ->get();
            if (sizeof($aux) == 0) {
                $dado = new DadosApp;
                $dado->id_paciente = $dados_app['person_id'];
                $dado->tipo_dados = $dados_app['type_id'];
                $dado->inicial = $dinicial->format('Y-m-d H:i:s');
                $dado->final = $dfinal->format('Y-m-d H:i:s');
                $dado->data = $dinicial->format('Y-m-d');     
                $dado->valor = $dados_app['value'];
                $dado->save();
            }
        }
    }
    public function getHealthResume(Request $request)
    {
        $array_principal = array();
        $url = "http://vps.targetclient.com.br/saude-beta/img/";
        $sLabel_ar = array(
            "Energia Ativa",
            "Energia de Repouso",
            "Horas em pé",
            "Percentual de gordura",

            "Batimentos",
            "Frequência Respiratória",
            "Monitor do Sono",

            "Passos",
            "Distância a Pé + Correndo",
            "Distância de Bicicleta",
            "Distância Nadando"
        );
        $sValue2_ar = array(
            "cal",
            "cal",
            "hrs",
            "%",

            "bpm",
            "respirações/min",
            "min/dia",

            "Passos",
            "km/dia",
            "km/dia",
            "m/dia"
        );
        $sImgs = array(
            $url . "calorias.png",
            $url . "calorias.png",
            $url . "calorias.png",
            $url . "calorias.png",

            $url . "Batimento.png",
            $url . "pulmao.png",
            $url . "cama.png",

            $url . "tenis-de-corrida.png",
            $url . "corrida.png",
            $url . "bicicleta.png",
            $url . "touca-de-natacao.png"
        );
        $sContent_ar = array(
            "Esta é uma estimativa da energia queimada e acima do seu uso de Energia de Repouso.",
            "Isto é uma estimativa da energia que seu corpo usa a cada dia, enquanto minimamente ativo.",
            "Os Minutos em Pé são os minutos de cada hora em que você fica em pé e se movimenta.",
            "Percentual de gordura corporal",

            "Refere-se a quantas vezes seu coração bate por minuto, e podem ser um indicador de saúde cardiovascular.",
            "Refere-se a quantas vezes você respira por minuto",
            "Tempo de sono",

            "A contagem de passos é o número de passos que você percorre ao longo do dia.",
            "Distância em km que você caminhou e correu no dia.",

            "Distância em km que você pedalou no dia",
            "Distância em m que você nadou no dia"
        );
        $interval_ar = array(0, 0, 0, 1, 1, 1, 2, 0, 0, 0, 0);
        for ($i = 0; $i < 11; $i++) {
            // if ($i == 0) {
            $info = new \StdClass;
            $info->iCardIndex = $i + 1;
            $info->sLabel = $sLabel_ar[$i];
            $info->sContent = $sContent_ar[$i];

            $info->sValue1 = '0';
            $info->sValue2 = $sValue2_ar[$i];
            $info->sValue3 = '';
            $info->sValue4 = $request->iPersonID;

            $info->sImg = $sImgs[$i];

            if ($interval_ar[$i] == 0) {
                $value = DB::table('dados_app')
                    ->selectRaw("SUM(CONVERT(valor, DECIMAL)) AS value,
                                          MAX(inicial) AS inicial")
                    ->where('tipo_dados', ($i + 1))
                    ->where('id_paciente', $request->iPersonID)
                    ->where('data', date('Y-m-d'))
                    ->first();
            } 
            else if ($interval_ar[$i] == 2) {
                $value = DB::table('dados_app')
                    ->selectRaw("(SUM(CONVERT(SUBSTRING(CONVERT(TIMEDIFF(final, inicial), char), 1, 2), decimal))*60) + (SUM(CONVERT(SUBSTRING(CONVERT(TIMEDIFF(final, inicial), char), 3, 2), decimal))*60) AS value,
                                          MAX(inicial) AS inicial")
                    ->where('tipo_dados', ($i + 1))
                    ->where('id_paciente', $request->iPersonID)
                    ->where('data', date('Y-m-d'))
                    ->first();
            }
            else {
                $value = DB::table('dados_app')
                    ->selectRaw("valor AS value,
                                          inicial AS inicial")
                    ->where('tipo_dados', ($i + 1))
                    ->where('id_paciente', $request->iPersonID)
                    ->where('data', date('Y-m-d'))
                    ->orderBy('final', 'DESC')
                    ->first();

            }
            if ($value) {
                if ($info->iCardIndex == 4) {
                    $info->sValue1= $value->value * 100;
                }
                else if ($info->iCardIndex == 3) {
                    $info->sValue1 = intVal(intVal($info->sValue1)/60)/60;
                    $info->sValue1 = number_format($info->sValue1, 2, '.', '');
                }
                else if (in_array($info->iCardIndex, array(5,6,7,8))) {
                    $info->sValue1 = intval($value->value);
                }
                else if (in_array($info->iCardIndex, array(9,10,11))) {
                    $info->sValue1 = (intVal($value->value)/1000);
                }
                else if ($value->value != '') {
                    $info->sValue1 = intval($value->value);
                }

                // if ($info->iCardIndex == 1 || $info->iCardIndex == 2 || $info->iCardIndex == 4 || $info->iCardIndex == 5 || $info->iCardIndex == 6 || $info->iCardIndex == 7) {
                //     $info->sValue1 = intval($value->value);
                // }
                $info->svalue3 = $value->inicial;

            }

            array_push($array_principal, $info);
        }
        return json_encode($array_principal);
    }

    public function getMedicalRecord(Request $request)
    {
        $array_principal = array();
        $labels = array(
            'IEC - Áreas Sugeridas',
            'Contratos',
            'Atividades',
            'Anamneses',
            'Documentos & Exames'
        );
        // return $labels;
        // return $request->iP
        for ($i = 0; $i < sizeof($labels); $i++) {
            // IEC \\
            if ($i == 0) {
                $card = new \StdClass;
                $card->iCardIndex = $i;
                $card->sImage = '';
                $card->sLabel = $labels[$i];
                $card->sContent = '';
                $card->sValue1 = '';
                $card->sValue2 = '';
                $card->sValue3 = '';
                $card->sValue4 = '';
                $card->sValue5 = '';
                $card->sValue6 = '';
                $card->sValue7 = '';
                $card->sValue8 = '';
                $card->sValue9 = '';

                $areas_sugeridas = DB::select(
                    DB::raw("
                        SELECT
                            IEC_questao_area.id_area

                        FROM
                            IEC_pessoa
                            left outer join IEC_pessoa_resp  on IEC_pessoa_resp.id_iec_pessoa = IEC_pessoa.id
                            left outer join IEC_questao_area on IEC_questao_area.id_questao   = IEC_pessoa_resp.id_questao

                        WHERE
                            IEC_pessoa.destacar = 'S'
                            AND IEC_pessoa.id_paciente = " . $request->iPersonID . "

                        GROUP BY
                            IEC_questao_area.id_area
                        LIMIT 9
                    ")
                );

                for ($i2 = 0; $i2 < sizeof($areas_sugeridas); $i2++) {
                    if ($i2 == 0 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue1 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 1 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue2 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 2 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue3 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 3 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue4 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 4 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue5 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 5 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue6 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 6 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue7 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 7 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue8 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                    if ($i2 == 8 && $areas_sugeridas[$i2]->id_area != null && $areas_sugeridas[$i2]->id_area)
                        $card->sValue9 = 'http://vps.targetclient.com.br/saude-beta/img/areas/' . $areas_sugeridas[$i2]->id_area . '.png';
                }
                array_push($array_principal, $card);
            }
            // CONTRATOS \\
            if ($i == 1) {
                $card = new \StdClass;
                $card->iCardIndex = $i;
                $card->sLabel = $labels[$i];
                $card->sContent = "Consulte os planos contratados e suas validades para consumo.";

                $aux_contrato = DB::select(
                    DB::raw("
                        Select 
                        id,
                        status,
                        MAX(data_validade) AS data,
                        SUM(atv_consu) AS atv_consu
                    from 
                        (
                        select 
                            pedido_principal.id AS id,
                            min(pedido_principal.data_validade) AS data_validade,
                            pedido_principal.status As status,
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
                            ) AS atv_consu
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
                            pedido_principal.lixeira = 0 
                            and pedido_principal.tipo_contrato = 'N' 
                            
                            and ((agenda.lixeira = 0 and agenda.status in ('F', 'A')) or agenda.id is null)
                            and pedido_principal.id_paciente = " . $request->iPersonID . "
                        group by 
                            pedido_principal.id,
                            forma_pag.descr,
                            pedido_principal.status
                        union all 
                            (
                            select 
                                old_contratos.id AS id,
                                MIN(old_contratos.datafinal) AS data_validade,
                                old_contratos.situacao AS status,
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
                                ) AS atv_consu
                            from 
                                old_contratos 
                                left join pessoa on pessoa.id = old_contratos.pessoas_id 
                                left join old_atividades on old_atividades.id_contrato = old_contratos.id
                                left join old_modalidades on old_modalidades.id = old_atividades.id_modalidade
                                left join old_finanreceber on old_finanreceber.id_contrato = old_contratos.id
                                left join old_plano_pagamento on old_plano_pagamento.id = old_finanreceber.id_planopagamento
                            where 
                                tipo_contrato = '' 
                                and id_periodo_contrato not in (1,7)
                                and old_contratos.pessoas_id = " . $request->iPersonID . "
                            group by 
                                old_contratos.id,
                                old_contratos.situacao
                            )
                        ) AS tab_aux
                    group by
                        id,
                        status
                    ")
                );
                // return $aux_contrato;
                $card->sValue1 = 0;
                $card->sValue2 = 0;
                $card->sValue3 = 0;
                // return $aux_contrato;
                foreach ($aux_contrato as $contrato) {
                    if ($contrato->atv_consu > 0 && strtotime($contrato->data) >= strtotime(date('Y-m-d')) && $contrato->status <> 'C') {
                        $card->sValue1++;
                    } else if ($contrato->atv_consu > 0 && strtotime($contrato->data) < strtotime(date('Y-m-d')) && $contrato->status <> 'C') {
                        $card->sValue2++;
                    } else if ($contrato->status == 'C')
                        $card->sValue3++;
                    else {
                        $card->sValue2++;
                    }
                }
                array_push($array_principal, $card);

            }
            // ATIVIDADES \\
            else if ($i == 2) {
                $card = new \StdClass;
                $card->iCardIndex = $i;
                $card->sImage = '';
                $card->sLabel = $labels[$i];
                $card->sContent = "Acompanhe por área da saúde a quantidade de atividades disponíveis para agendamento";

                $pedidos = DB::table('pedido')
                    ->selectRaw('Group_Concat(pedido.id) AS ids')
                    ->where('lixeira', 0)
                    ->where('status', 'F')
                    ->where('id_paciente', $request->iPersonID)
                    ->groupBy('id_paciente')
                    ->value('ids');
                $pedidos = "(" . $pedidos . ")";


                if ($pedidos != '()') {
                    $max_atv = DB::table('pedido_planos')
                        ->select(DB::raw('SUM(pedido_planos.qtde * pedido_planos.qtd_total) AS total'))
                        ->leftjoin('pedido', 'pedido.id', 'pedido_planos.id_pedido')
                        ->whereRaw('pedido_planos.id_pedido in ' . $pedidos)
                        ->groupBy('id_paciente')
                        ->value('total');

                    $agendados = DB::table('agenda')
                        ->whereRaw('agenda.id_pedido in ' . $pedidos . "AND 
                                            lixeira = 0 AND
                                            status = 'A'")
                        ->count();



                    $total_consu = DB::table('agenda')
                        ->whereRaw('agenda.id_pedido in ' . $pedidos . "AND 
                                                lixeira = 0 AND
                                                status in ('A', 'F')")
                        ->count();
                } else {
                    $max_atv = 0;
                    $total_consu = 0;
                }

                $card->sValue1 = ($max_atv - $total_consu);
                array_push($array_principal, $card);

            }
            // ANAMNESE \\
            else if ($i == 3) {
                $card = new \StdClass;
                $card->iCardIndex = $i;
                $card->sImage = '';
                $card->sLabel = $labels[$i];
                $card->sContent = "Visualize o histórico das anamneses respondidas";

                $card->sValue1 = sizeof(
                    DB::table('anamnese_pessoa')
                        ->select('anamnese_pessoa.id')
                        ->where('anamnese_pessoa.id_pessoa', $request->iPersonID)
                        ->get()
                );
                $card->sValue2 = 0;
                $card->sValue3 = 0;
                array_push($array_principal, $card);

            }
            // DOCUMENTOS \\
            else if ($i == 4) {
                $card = new \StdClass;
                $card->iCardIndex = $i;
                $card->sImage = '';
                $card->sLabel = $labels[$i];
                $card->sContent = "Visualize ou envie documentos e exames para avalicação dos profissionais";
                $card->sValue1 = sizeof(
                    DB::table('anexos')
                        ->select('anexos.id')
                        ->where('id_paciente', $request->iPersonID)
                        ->get()
                );
                $card->sValue2 = 0;
                $card->sValue3 = 0;
                array_push($array_principal, $card);
            }
        }
        return $array_principal;
    }

    public function exportPdf()
    {
        $pdf = PDF::loadView('index'); // <--- load your view into theDOM wrapper;
        $path = public_path('pdf_docs/'); // <--- folder to store the pdf documents into the server;
        $fileName = time() . '.' . 'pdf'; // <--giving the random filename,
        $pdf->save($path . '/' . $fileName);
        $generated_pdf_link = url('pdf_docs/' . $fileName);
        return response()->json($generated_pdf_link);
    }
    public function getActivities(Request $request)
    {
        $pedidos = DB::table('pedido')
            ->select(
                DB::raw("(select 0) AS iCardIndex"),
                DB::raw("
                    CASE WHEN (
                        (
                            SELECT 
                                COUNT(modalidades_por_plano.id)
                            FROM
                                pedido_planos AS p2
                            WHERE
                                p2.id_pedido = pedido.id
                            GROUP BY
                                p2.id_pedido
                        ) > 1
                    )
                    THEN 'HABILITAÇÃO' 
                    ELSE GROUP_CONCAT(distinct procedimento.descr) 
                    END AS sLabel
                 "),
                DB::raw("
                    CASE WHEN (
                        (
                            SELECT 
                                COUNT(m2.id)
                            FROM
                                modalidades_por_plano As m2
                                left join pedido_planos As pp2 on pp2.id_plano = m2.id_tabela_preco
                            WHERE
                                pp2.id_pedido = pedido.id
                            GROUP BY
                                pp2.id_pedido
                        ) > 1
                    )
                    THEN (select 'http://vps.targetclient.com.br/saude-beta/img/areas/habilitacao.png')
                    ELSE (select CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', MIN(procedimento.id), '.png')) END AS sImg
                 "),
                DB::raw("MIN(pedido_planos.qtde * qtd_total) AS sValue1"),
                DB::raw("DATE_FORMAT(pedido.data_validade,'%d/%m/%Y') AS sValue2")
            )
            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
            ->leftjoin('modalidades_por_plano', 'modalidades_por_plano.id_tabela_preco', 'pedido_planos.id_plano')
            ->leftjoin('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
            ->where('pedido.id_paciente', $request->iPersonID)
            ->where('pedido.lixeira', 0)
            ->where('pedido.tipo_contrato', 'N')
            ->groupBy(
                'pedido.id'
                //   'procedimento.id',
            )
            // ->unionAll(
            //     DB::table('old_atividades')
            //     ->select(DB::raw("(select 0) AS iCardIndex"),
            //             'old_modalidades.descr AS sLabel',
            //             DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/areas/', old_modalidades.id, '.png') AS sImg"),
            //             DB::raw("SUM(old_atividades.qtd) AS sValue1"),
            //             'old_contratos.datafinal AS sValue2')
            //     ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
            //     ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
            //     ->where('old_contratos.situacao', '<>', 'C')
            //     ->where('old_atividades.qtd', '>', '0')
            //     ->where('old_contratos.pessoas_id', $request->iPersonID)
            //     ->groupBy('old_modalidades.descr',
            //             'old_modalidades.area_modalidade',
            //             'old_atividades.id',
            //             'old_modalidades.id',
            //             'old_contratos.datafinal')
            // )
            ->get();

        for ($i = 0; $i < sizeof($pedidos); $i++) {
            $pedidos[$i]->iCardIndex = $i;
        }
        return json_encode($pedidos);
    }

    public function pedidos_por_pessoa(Request $request) {
        return json_encode(DB::select(DB::raw("
            SELECT id FROM pedido
        
            WHERE pedido.data_validade >= CURDATE()
              AND pedido.lixeira = 0
              AND pedido.id_paciente = ".$request->pessoa."
        ")));
    }

    public function planos_por_pedido(Request $request) {
        return json_encode(DB::select(DB::raw("
            SELECT tabela_precos.id, tabela_precos.descr AS plano
            FROM pedido_planos
            JOIN tabela_precos
                ON tabela_precos.id = pedido_planos.id_plano
            WHERE tabela_precos.lixeira = 0
              AND pedido_planos.id_pedido = ".$request->pedido."
        ")));
    }

    public function planos_por_pessoa(Request $request) {
        $consulta = DB::select(DB::raw("
            SELECT
                pedido_planos.id           AS id_pp,
                tabela_precos.id           AS id_plano,
                tabela_precos.descr        AS descr_plano,
                pedido.id                  AS id_pedido,
                DATE(pedido.data_validade) AS data_validade,
                aux.ct,
                tabela_precos.max_atv,
                tabela_precos.vigencia,
                tabela_precos.max_atv_semana
            
            FROM pedido
            
            JOIN pedido_planos
                ON pedido_planos.id_pedido = pedido.id
                
            JOIN tabela_precos
                ON tabela_precos.id = pedido_planos.id_plano

            LEFT JOIN (
                SELECT
                    id_pedido,
                    COUNT(agenda.id) AS ct
                
                FROM agenda
                
                WHERE id_pedido <> 0
                  AND lixeira = 0
                  AND status <> 'C'

                GROUP BY
                    id_pedido
            ) AS aux ON aux.id_pedido = pedido.id
                
            WHERE pedido.lixeira = 0
              AND tabela_precos.lixeira = 0
              AND pedido.data_validade >= CURDATE()
              AND pedido.id_paciente = ".$request->pessoa."
            
            ORDER BY pedido.data_validade
        "));
        $resultado = array();
        foreach($consulta as $linha) {
            $total_atv = ($linha->max_atv == 0) ? intval(($linha->vigencia / 7) * $linha->max_atv_semana) : $linha->max_atv;
            $linha->restantes = $total_atv - $linha->ct;
            if (sizeof($this->modalidades_por_plano_main($request->empresa, $linha->id_plano))) array_push($resultado, $linha);
        }
        return json_encode($resultado);
    }

    public function modalidades_por_plano(Request $request) {
        return json_encode($this->modalidades_por_plano_main($request->empresa, $request->plano));
    }

    private function modalidades_por_plano_main($empresa, $plano) {
        return DB::select(DB::raw("
            SELECT
                especialidade.id,
                especialidade.descr AS categoria,
                procedimento.id AS id_procedimento,
                procedimento.descr
                    
            FROM modalidades_por_plano
                        
            JOIN procedimento
                ON procedimento.id = modalidades_por_plano.id_procedimento
                
            JOIN especialidade
                ON especialidade.id = procedimento.id_especialidade

            WHERE procedimento.lixeira = 0
              AND especialidade.lixeira = 0
              /*AND procedimento.id_emp = ".$empresa."
              AND modalidades_por_plano.id_empresa = ".$empresa."*/
              AND modalidades_por_plano.id_tabela_preco = ".$plano."

            GROUP BY
                id,
                descr,
                id_procedimento
        "));
    }

    public function emp() {
        return json_encode(DB::select(DB::raw("
            SELECT
                empresa.id,
                empresa.descr,
                empresa.endereco,
                empresa.cidade,
                empresa.uf
                
            FROM empresa
        ")));
    }

    public function profissionais_por_modalidade(Request $request) {
        $consulta = DB::select(DB::raw("
            SELECT
                pessoa.id,
                pessoa.nome_fantasia AS profissional,
                CASE
                    WHEN celular1 IS NOT NULL THEN celular1
                    WHEN celular2 IS NOT NULL THEN celular2
                    WHEN telefone1 IS NOT NULL THEN telefone1
                    WHEN telefone2 IS NOT NULL THEN telefone2
                    ELSE telefone2
                END AS telefone,
                email
                
            FROM pessoa
            
            JOIN especialidade_pessoa
                ON especialidade_pessoa.id_profissional = pessoa.id
            
            JOIN empresas_profissional
                ON empresas_profissional.id_profissional = pessoa.id
            
            WHERE pessoa.lixeira = 0
              AND pessoa.colaborador IN ('P', 'A')
              AND empresas_profissional.id_emp = ".$request->empresa."
              AND especialidade_pessoa.id_especialidade = ".$request->modalidade."
              AND pessoa.id != 28480001953
            GROUP BY
                id,
                profissional
        "));
        $resultado = array();
        foreach($consulta as $linha) {
            if (file_exists(public_path('img') . '/pessoa/1/' . $linha->id . '.jpg')) $url = public_path('img') . '/pessoa/1/' . $linha->id . '.jpg';
            else if (file_exists(public_path('img') . '/pessoa/2/' . $linha->id . '.jpg')) $url = public_path('img') . '/pessoa/2/' . $linha->id . '.jpg';
            else if (file_exists(public_path('img') . '/pessoa/' . $linha->id . '.jpg')) $url = public_path('img') . '/pessoa/' . $linha->id . '.jpg';
            else $url = "404";
            $linha->url = $url;
            array_push($resultado, $linha);
        }
        return json_encode($resultado);
    }

    public function listarHorarios(Request $request) {
        return json_encode(DB::select(DB::raw("
            SELECT
                grade_horario.id,
                grade_horario.hora,
                CASE
                    WHEN grade_horario.dia_semana = 0 THEN 'sáb'
                    WHEN grade_horario.dia_semana = 1 THEN 'dom'
                    WHEN grade_horario.dia_semana = 2 THEN 'seg'
                    WHEN grade_horario.dia_semana = 3 THEN 'ter'
                    WHEN grade_horario.dia_semana = 4 THEN 'qua'
                    WHEN grade_horario.dia_semana = 5 THEN 'qui'
                    ELSE 'sex'
                END AS dia_semana
                
            FROM grade_horario

            JOIN grade
                ON grade.id = grade_horario.id_grade
                
            LEFT JOIN agenda
                ON agenda.hora = grade_horario.hora
                    AND agenda.lixeira = 0
                    AND agenda.status <> 'C'
                    AND agenda.data = '".$request->data."'
                    AND agenda.id_emp = grade.id_emp
                    AND agenda.id_profissional = grade.id_profissional

            LEFT JOIN grade_bloqueio
                ON grade_horario.hora BETWEEN grade_bloqueio.hora_inicial AND grade_bloqueio.hora_final
                    AND '".$request->data."' BETWEEN grade_bloqueio.data_inicial AND grade_bloqueio.data_final
                    AND grade_bloqueio.ativo = 1
                    AND grade_bloqueio.id_profissional = grade.id_profissional
                    AND grade_bloqueio.dia_semana = grade.dia_semana
                    AND grade_bloqueio.id_emp = grade.id_emp
                
            WHERE (grade.data_final > '".$request->data."' OR grade.data_final IS NULL)
              AND grade.data_inicial < '".$request->data."'
              AND grade.id_emp = ".$request->empresa."
              AND grade.id_profissional = ".$request->profissional."
              AND grade_horario.dia_semana = (WEEKDAY('".$request->data."')+2)
              AND agenda.id IS NULL
              AND grade_bloqueio.id IS NULL
            
              AND grade.ativo = 1
              AND grade.lixeira = 'N'
            
            GROUP BY
                id,
                hora
                
            ORDER BY hora
        ")));
    }

    public function agendar(Request $request) {
        $agendamento = new Agenda;
        $agendamento->id_emp = $request->empresa;
        $agendamento->id_profissional = $request->profissional;
        $agendamento->id_paciente = $request->paciente;
        $agendamento->id_procedimento = 1;
        $agendamento->id_tipo_procedimento = $request->procedimento;
        $agendamento->id_grade_horario = $request->id_grade_horario;
        $agendamento->id_convenio = $request->convenio;
        $agendamento->id_status = 6;
        $agendamento->id_confirmacao = 0;
        $agendamento->id_reagendado = 0;
        $agendamento->id_pedido = $request->id_pedido;
        $agendamento->id_tabela_preco = $request->plano;
        $agendamento->id_modalidade = $request->modalidade;
        $agendamento->data = $request->data;
        $agendamento->hora = GradeHorario::find($request->id_grade_horario)->hora;
        $agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
        $agendamento->obs = '';
        $agendamento->status = 'A';
        $agendamento->motivo_cancelamento = 0;
        $agendamento->obs_cancelamento = '';
        $agendamento->reagendamento = isset($request->id_antigo);
        $agendamento->bordero = true;
        $agendamento->lixeira = false;
        $agendamento->created_by = 'app';
        $agendamento->updated_by = 'app';
        $agendamento->save();
        $auxdt = date('d/m/Y', strtotime($request->data));
        if (isset($request->id_antigo)) {
            DB::statement("
                UPDATE agenda SET
                    status = 'C',
                    id_status = 7,
                    motivo_cancelamento = 4,
                    obs_cancelamento = 'Reagendado para ".$auxdt." ".$agendamento->hora."'
                WHERE id = ".$request->id_antigo
            );
        }
        $notificacao = new Notificacao;
        $notificacao->id_emp = $request->empresa;
        $notificacao->id_paciente = $request->paciente;
        $notificacao->assunto = "Agendamento realizado";
        $notificacao->notificacao = "Agendamento feito para ".Pessoa::find($request->paciente)->nome_fantasia." em ".$auxdt." às ".$agendamento->hora;
        $notificacao->publico = false;
        $notificacao->id_profissional = $request->profissional;
        $notificacao->created_by = 0;
        $notificacao->lixeira = false;
        $notificacao->save();
    }
    
    public function grade_por_modalidade(Request $request) {
        return json_encode(DB::select(DB::raw("
            SELECT
                grade_horario.id,
                grade_horario.dia_semana,
                grade_horario.hora
            
            FROM grade_horario

            JOIN grade
                ON grade.id = grade_horario.id_grade    

            WHERE grade.id_profissional IN (
                SELECT pessoa.id
                FROM pessoa
                JOIN especialidade_pessoa
                    ON especialidade_pessoa.id_profissional = pessoa.id
                JOIN empresas_profissional
                    ON empresas_profissional.id_profissional = pessoa.id
           
                WHERE pessoa.lixeira = 0
                  AND pessoa.colaborador IN ('P', 'A')
                  AND empresas_profissional.id_emp = ".$request->empresa."
                  AND especialidade_pessoa.id_especialidade = ".$request->modalidade."
                
                GROUP BY id
            ) AND grade.ativo = 1
              AND grade.lixeira = 'N'
              AND grade.id_emp = ".$request->empresa."
        ")));
    }

    public function getDocs(Request $request)
    {
        $docs = DB::table('anexos')
            ->select(
                'anexos.id',
                DB::raw("(select 0) AS iCardIndex"),
                'anexos.obs As sLabel',
                'pessoa.nome_fantasia AS sContent',
                'anexos.id_profissional',
                DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/api/baixar-anexo/', anexos.id) AS sValue1"),
                DB::raw("DATE_FORMAT(anexos.created_at, '%d/%m/%Y') AS sValue2")
            )
            ->leftjoin('pessoa', 'pessoa.id', 'anexos.id_paciente')
            ->where('anexos.id_paciente', $request->iPersonID)
            ->orderBy('anexos.id', 'DESC')
            ->get();

        for ($i = 0; $i < sizeof($docs); $i++) {
            $docs[$i]->iCardIndex = $i;
        }
        return json_encode($docs);
    }

    public function baixarDoc($id)
    {
        // return 'sjodajs';
        return redirect('http://vps.targetclient.com.br/saude-beta/anexos/' . str_replace(' ', '%20', Anexos::find($id)->titulo));
        //return response()->file('http://vps.targetclient.com.br/saude-beta/anexos/' . str_replace(' ', '%20', Anexos::find($id)->titulo));
    }

    public function deleteAnexo(Request $request) {
        Anexos::find($request->id)->delete();

        return json_encode(["response"=>"true"]);
    }

    // public function getAvatar(Request $request){
    //     return 
    // }

    // public function login(Request $request){
    //     $login = DB::table('usersApp')
    //             ->where('email', $request->email)
    //             ->first();


    //     $data = new \StdClass;
    //     if ($login){
    //         if($login->senha === $request->senha) {
    //             $data->id = $login->id_pessoa;
    //             $data->pass = true;
    //         }
    //         else {
    //             $data->pass = false;
    //             $data->error = "Senha Incorreta";
    //         }
    //     }
    //     else {
    //         $data->error = "Usuário não encontrato";
    //         $data->pass = false;
    //     }
    //     return json_encode($data);
    // }
    public function getDataPerson(Request $request)
    {

        $pessoa = DB::table('pessoa')
            ->select(
                'pessoa.data_nasc AS data_nasc',
                'pessoa.sexo',
                DB::raw("(select 200) AS status"),
                DB::raw("CONCAT('http://vps.targetclient.com.br/saude-beta/img/pessoa/', pessoa.id, '.jpg') as avatar")
            )
            ->where('pessoa.id', $request->iPersonID)
            ->first();
        $pessoa->data_nasc = intval(date('Y')) - intval(date('Y', strtotime($pessoa->data_nasc)));
        return json_encode($pessoa);
    }
    public function getIECs(Request $request)
    {
        $IECs = DB::table('IEC_pessoa')
            ->select(
                DB::raw("(select 1) As iCardIndex"),
                'IEC_questionario.descr AS sLabel',
                'pessoa.nome_fantasia AS sContent',
                DB::raw("DATE_FORMAT(IEC_pessoa.created_at ,'%d/%m/%Y') AS sValue1"),
                DB::raw("DATE_FORMAT(IEC_pessoa.created_at, '%H : %i') AS sValue2"),
                "IEC_pessoa.id AS sValue4"
            )
            ->leftjoin('IEC_questionario', 'IEC_questionario.id', 'IEC_pessoa.id_questionario')
            ->leftjoin('pessoa', 'pessoa.id', 'IEC_pessoa.id_membro')
            ->where('IEC_pessoa.id_paciente', $request->iPersonID)
            ->where('IEC_pessoa.lixeira', 0)
            ->where('IEC_questionario.ativo', 'S')
            ->orderBy('IEC_pessoa.created_at', 'DESC')
            ->get();

        for ($i = 0; $i < sizeof($IECs); $i++) {
            $IECs[$i]->iCardIndex = $i;
        }
        return json_encode($IECs);
    }


    public function getAnamneses(Request $request)
    {
        $anamneses = DB::table('anamnese_pessoa')
            ->select(
                'anamnese_pessoa.id  AS iCardIndex',
                'anamnese.descr       AS sLabel',
                'membro.nome_fantasia AS sContent',
                DB::raw(
                    "CONCAT(DATE_FORMAT(anamnese_pessoa.data, '%d/%m/%Y'), ' - ', SUBSTRING(anamnese_pessoa.hora, 0, 5)) AS sValue1"
                ),
                'anamnese_pessoa.id AS sValue2'
            )
            ->leftjoin('anamnese', 'anamnese.id', 'anamnese_pessoa.id_anamnese')
            ->leftjoin('pessoa AS membro', 'membro.id', 'anamnese_pessoa.id_membro')
            ->where('anamnese_pessoa.id_pessoa', $request->iPersonID)
            // ->where('anamnese_pessoa.publico', 'S')
            ->orderBy('anamnese_pessoa.id', 'DESC')
            ->get();

        for ($i = 0; $i < sizeof($anamneses); $i++) {
            $anamneses[$i]->iCardIndex = $i;
        }

        return json_encode($anamneses);
    }

    public function getContratos(Request $request)
    {
        $contratos = DB::table('pedido')
            ->select(
                'pedido.id  AS iCardIndex',
                DB::raw("CONCAT('Contratado em ', DATE_FORMAT(pedido.data, '%d/%m/%Y')) AS sLabel"),
                DB::raw("CONCAT('Consultor de vendas: ', pedido.consultor) AS sContent"),
                'pedido.id AS sValue2',
                DB::raw("(select 0) As sValue3")
            )
            ->where('pedido.id_paciente', $request->iPersonID)
            ->where('pedido.lixeira', 0)
            ->where('pedido.tipo_contrato', 'N')
            ->orderBy('pedido.id', 'DESC')
            ->unionAll(
                DB::table('old_contratos')
                    ->selectRaw("old_contratos.id AS iCardIndex,
                                          CONCAT('Contratado em ', DATE_FORMAT(old_contratos.datainicial, '%d/%m/%Y')) AS sLabel,
                                          CONCAT('Consultor de vendas: ', old_contratos.responsavel) AS sContent,
                                          old_contratos.id As sValue2,
                                          (select 1) as sValue3")
                    ->whereRaw("tipo_contrato = '' 
                                        and id_periodo_contrato not in (1,7)
                                        and old_contratos.pessoas_id = " . $request->iPersonID . "")
            )
            ->get();

        for ($i = 0; $i < sizeof($contratos); $i++) {
            $contratos[$i]->iCardIndex = $i;
        }

        return json_encode($contratos);
    }

    function mostrar_resposta_iec($id)
    {
        try {
            $IEC = new \stdClass;
            $IEC_pessoa = IECPessoa::find($id);
            $IEC->id = $IEC_pessoa->id_questionario;
            $IEC->descr = DB::table('IEC_questionario')
                // ->where('id_emp', getEmpresa())
                ->where('id', $IEC->id)
                ->value('descr');

            $IEC->perguntas = DB::table('IEC_questao')
                ->select('id', 'pergunta')
                ->where('id_questionario', $IEC->id)
                ->get();
                $obs = $IEC_pessoa->obs;
            $respostas = array();
            $valores = array();
            $id_areas_sugeridas = array();

            foreach ($IEC->perguntas as $pergunta) {

                $j = 0;
                $resposta = DB::table('IEC_pessoa_resp')
                    ->where('id_questao', $pergunta->id)
                    ->where('id_iec_pessoa', $id)
                    ->value('resposta');
                $resposta_str = IECQuestao::find($pergunta->id);
                $area = DB::table('IEC_questao_area')
                    ->selectRaw("IEC_questao_area.*")
                    ->leftjoin('especialidade', 'especialidade.id', 'IEC_questao_area.id_area')
                    ->where('id_questao', $pergunta->id)
                    ->where('status', $resposta)
                    ->where('especialidade.lixeira', 0)
                    ->get();

                array_push($id_areas_sugeridas, $area);
                array_push($valores, $resposta);
                switch ($resposta) {
                    case 1:
                        array_push($respostas, $resposta_str->pessimo);
                        break;
                    case 2:
                        array_push($respostas, $resposta_str->ruim);
                        break;
                    case 3:
                        array_push($respostas, $resposta_str->bom);
                        break;
                    case 4:
                        array_push($respostas, $resposta_str->excelente);
                        break;
                }

            }

            $membro = DB::table('pessoa')
                ->where('id', $IEC_pessoa->id_membro)
                ->value('nome_fantasia');

            $pessoa = DB::table('pessoa')
                ->where('id', $IEC_pessoa->id_paciente)
                ->value('nome_fantasia');
            // return json_encode($respostas);
            return view('reports.impresso_IEC', compact('IEC', 'respostas', 'membro', 'pessoa', 'anamnese_pessoa', 'valores', 'id_areas_sugeridas', 'obs'));

            return json_encode($anamnese->respostas);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function mostrar_resposta_anamnese($id)
    {
        try {
            $anamnese = new \stdClass;
            $anamnese_pessoa = AnamnesePessoa::find($id);
            $anamnese->id = $anamnese_pessoa->id_anamnese;
            $anamnese->descr = DB::table('anamnese')
                // ->where('id_emp', getEmpresa())
                ->where('id', $anamnese->id)
                ->value('descr');

            $anamnese->perguntas = DB::table('anamnese_pergunta')
                ->select('id', 'pergunta')
                ->where('id_anamnese', $anamnese->id)
                ->get();
            $respostas = array();
            foreach ($anamnese->perguntas as $pergunta) {
                $resposta = DB::table('anamnese_resposta')
                    ->where('id_pergunta', $pergunta->id)
                    ->where('id_anamnese_pessoa', $id)
                    ->value('resposta');
                array_push($respostas, $resposta);
            }

            $membro = DB::table('pessoa')
                ->where('id', $anamnese_pessoa->id_membro)
                ->value('nome_fantasia');

            $pessoa = DB::table('pessoa')
                ->where('id', $anamnese_pessoa->id_pessoa)
                ->value('nome_fantasia');



            // foreach ($anamnese->perguntas as $pergunta) {
            //     if ($pergunta->tipo == 'C') {
            //         $pergunta->opcoes = DB::table('anamnese_opcao')
            //                             ->where('id_pergunta', $pergunta->id)
            //                             ->get();
            //     }
            // }

            return view('reports.impresso_anamnese', compact('anamnese', 'respostas', 'membro', 'pessoa', 'anamnese_pessoa'));
            return json_encode($anamnese->respostas);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar_contrato($id, $antigo)
    {
        try {
            $pedido_header = DB::table('pedido')
                ->select(
                    'pedido.id',
                    'pedido.id AS num_pedido',
                    'pedido.status',
                    'pedido.data_validade',
                    'empresa.descr as descr_emp',
                    'pedido.obs',
                    'paciente.nome_fantasia AS descr_paciente',
                    'prof_examinador.nome_fantasia AS descr_prof_exa',
                    'convenio.descr AS descr_convenio',
                    'pedido.data'
                )
                ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
                ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'pedido.id_prof_exa')
                ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                ->leftjoin('empresa', 'empresa.id', 'pedido.id_emp')
                ->where('pedido.id', $id)
                ->first();
            $pedido_header_old = DB::table('old_contratos')
                ->select(
                    DB::raw('old_contratos.id AS id'),
                    DB::raw('old_contratos.id AS num_pedido'),
                    DB::raw("CASE WHEN (old_contratos.situacao = 1) THEN (select 'F')
                                          ELSE old_contratos.situacao END AS status"),
                    DB::raw("CONCAT(datafinal, ' ', horafinal) AS data_validade"),
                    DB::raw("(select 'sistema antigo') AS obs"),
                    DB::raw("(Select 'ON - EVOLUÇÃO CORPORAL MORUMBI') AS descr_emp"),
                    'paciente.nome_fantasia AS descr_paciente',
                    DB::raw(" CASE WHEN (old_contratos.tipo_contrato = 'P') THEN (
                                        SELECT 
                                            usu_confirm 
                                        FROM 
                                            old_mov_atividades 
                                            INNER JOIN old_atividades ON old_mov_atividades.id_atividade = old_atividades.id 
                                        WHERE 
                                            old_atividades.id_contrato = old_contratos.id limit 1
                                        ) ELSE (
                                        SELECT 
                                            old_contratos.responsavel
                                        ) END AS descr_prof_exa"),
                    "old_financeira.descr AS descr_convenio",
                    "old_contratos.datainicial AS data"
                )
                ->leftjoin('pessoa AS paciente', 'paciente.id', 'old_contratos.pessoas_id')
                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_planopagamento')
                ->where('old_contratos.id', $id)
                ->first();

            $planos = DB::table('pedido_planos')
                ->select(
                    'tabela_precos.descr    AS descr',
                    'tabela_precos.vigencia AS vigencia',
                    'tabela_precos.valor    AS valor',
                    DB::raw("SUM(" .
                        "CASE WHEN (agenda.id is not NULL AND agenda.status in ('F', 'A') AND agenda.lixeira = 0) THEN (select 1)" .
                        "ELSE (select 0) END) AS atv_consu"),
                    DB::raw("(pedido_planos.qtd_total * pedido_planos.qtde) AS max_atv")
                )
                ->join('tabela_precos', 'tabela_precos.id', 'id_plano')
                ->leftjoin('agenda', function ($sql) {
                    $sql->on('agenda.id_pedido', '=', 'pedido_planos.id_pedido')
                        ->on('agenda.id_tabela_preco', '=', 'pedido_planos.id_plano');
                })
                ->where('pedido_planos.id_pedido', $id)
                //   ->where(function($sql) {
                //     $sql->where('agenda.lixeira', 0)
                //         ->orWhere('agenda.lixeira', 'is',null);
                //   })

                ->groupBy(
                    'tabela_precos.descr',
                    'tabela_precos.vigencia',
                    'tabela_precos.valor',
                    'pedido_planos.qtd_total',
                    'pedido_planos.qtde'
                )
                ->get();

            $planos_old = DB::table('old_atividades')
                ->select(
                    'old_modalidades.descr  AS  descr',
                    'old_atividades.periodo_dias     AS vigencia',
                    DB::raw("(old_atividades.valor_cardapio * old_atividades.qtd_ini) as valor"),
                    DB::raw('(old_atividades.qtd_ini - old_atividades.qtd) as atv_consu'),
                    "old_atividades.qtd_ini AS max_atv"
                )
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->where('old_atividades.id_contrato', $id)
                ->get();

            // return $planos_old;
            // if($antigo == 0){
            $pedido_formas_pag = DB::table('pedido_forma_pag')
                ->select(
                    'pedido_forma_pag.*',
                    'forma_pag.descr AS descr_forma_pag'
                )
                ->leftjoin('forma_pag', 'forma_pag.id', 'pedido_forma_pag.id_forma_pag')
                ->where('pedido_forma_pag.id_pedido', $id)
                ->get();

            foreach ($pedido_formas_pag as $pag) {
                $pag->parcelas = DB::table('pedido_parcela')
                    ->where('id_pedido_forma_pag', $pag->id)
                    ->get();
            }
            // }       
            // if ($pedido_header <> 'sistema_antigo'){
            $parcelas = DB::table('old_finanreceber')
                ->select(
                    'old_finanreceber.parcela',
                    'old_plano_pagamento.descr AS descr_forma_pag',
                    'old_finanreceber.valor AS valor',
                    'old_finanreceber.datavenc AS vencimento',
                    'old_finanreceber.id_planopagamento'
                )
                ->leftjoin('old_plano_pagamento', 'old_plano_pagamento.id', 'old_finanreceber.id_planopagamento')
                ->where('old_finanreceber.id_contrato', $id)
                ->whereRaw("old_finanreceber.financeira <> 'S'")
                ->orderBy('old_finanreceber.parcela')
                ->get();

            // }
            // else {
            //     $parcelas = DB::table('old_finanreceber')
            //                 ->select('old_finanreceber.parcela',
            //                             'old_plano_pagamento.descr AS descr_forma_pag',
            //                             'old_finanreceber.valor AS valor',
            //                             'old_finanreceber.datavenc AS vencimento',
            //                             'old_finanreceber.id_planopagamento')       
            //                 ->leftjoin('old_plano_pagamento', 'old_plano_pagamento.id', 'old_finanreceber.id_planopagamento')
            //                 ->where('old_finanreceber.id_contrato', $id)
            //                 ->whereRaw("old_finanreceber.financeira <> 'S'")
            //                 ->get();

            // }

            $emp_logo = null;
            $path = '';
            if (file_exists($path)) {
                $emp_logo = base64_encode(file_get_contents($path));
            }
            if ($antigo == 0) {
                return view('.reports.impresso_pedido2', compact('pedido_header', 'pedido_formas_pag', 'emp_logo', 'planos', 'antigo', 'parcelas'));
            } else {
                $pedido_header = $pedido_header_old;
                $planos = $planos_old;
                return view('.reports.impresso_pedido2', compact('pedido_header', 'parcelas', 'emp_logo', 'planos', 'antigo', 'pedido_formas_pag'));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    // GRAFICOS \\
    public function getGrafico($op, $iPersonID)
    {
        return view('graficos_app', compact('op', 'iPersonID'));
    }

    public function gerar_grafico_1($op, $iPersonID)
    {
        $labels_ar = array();
        $values_ar = array();

        $registros = DB::table('dados_app')
            ->selectRaw("valor AS value,
                                inicial AS inicial")
            ->where('tipo_dados', $op)
            ->where('id_paciente', $iPersonID)
            ->where('data', date('Y-m-d'))
            ->orderBy('dados_app.inicial')
            ->get();

        foreach ($registros as $registro) {
            $data = new \StdClass;

            if ($op == 4) {
                $registro->value == $registro->value * 100;
            }
            else if ($op == 3) {
                $aux = intVal(intVal($info->sValue1)/60)/60;
                $aux = number_format($info->sValue1, 2, '.', '');
                $registro->value = $aux;
            }
            else if (in_array($op, array(5,6,7,8))) {                    
                $registro->value = intval($registro->value);
            }
            else if (in_array($op, array(9,10,11))) {
                $registro->value = (intVal($registro->value)/1000);
            }
            else if ($registro->value != '') {
                $registro->value = intval($registro->value);
            }

            array_push($labels_ar, date("H:i", strtotime($registro->inicial)));
            array_push($values_ar, $registro->value);
        }

        $data = new \StdClass;

        $data->labels = $labels_ar;
        $data->values = $values_ar;
        return json_encode($data);
    }

    public function gerar_grafico_2($op, $iPersonID)
    {
        $labels_ar = array();
        $values_ar = array();

        for ($i = 6; $i >= 0; $i--) {
            if ($op == 0) {
                $registro = DB::table('dados_app')
                    ->selectRaw("SUM(CONVERT(valor, DECIMAL)) AS value,
                                            MAX(inicial) AS inicial")
                    ->where('tipo_dados', $op)
                    ->where('id_paciente', $iPersonID)
                    ->where('data', date('Y-m-d', strtotime("-" . $i . " days")))
                    ->first();
            } 
            else if ($op == 2) {
                $registro = DB::table('dados_app')
                    ->selectRaw("(SUM(CONVERT(SUBSTRING(CONVERT(TIMEDIFF(final, inicial), char), 1, 2), decimal))*60) + (SUM(CONVERT(SUBSTRING(CONVERT(TIMEDIFF(final, inicial), char), 3, 2), decimal))*60) AS value,
                                            MAX(inicial) AS inicial")
                    ->where('tipo_dados', $op)
                    ->where('id_paciente', $iPersonID)
                    ->where('data', date('Y-m-d', strtotime("-" . $i . " days")))
                    ->first();
            }
            else {
                $registro = DB::table('dados_app')
                    ->selectRaw("SUM(valor) AS value,
                                            MAX(inicial) AS inicial")
                    ->where('tipo_dados', $op)
                    ->where('id_paciente', $iPersonID)
                    ->where('data', date('Y-m-d', strtotime("-" . $i . " days")))
                    // ->orderBy('final', 'DESC')
                    ->first();

            }
            if ($registro) {
                if ($op == 4) {
                    array_push($values_ar, $registro->value * 100);
                }
                else if ($op == 3) {
                    $aux = intVal(intVal($info->sValue1)/60)/60;
                    $aux = number_format($info->sValue1, 2, '.', '');
                    array_push($values_ar, $aux);
                }
                else if (in_array($op, array(5,6,7,8))) {                    
                    array_push($values_ar, intval($registro->value));
                }
                else if (in_array($op, array(9,10,11))) {
                    array_push($values_ar, (intVal($registro->value)/1000));
                }
                else if ($registro->value != '') {
                    array_push($values_ar, intval($registro->value));
                }



                if ($registro->inicial != null)
                    array_push($labels_ar, date("d/m", strtotime($registro->inicial)));
                else
                    array_push($labels_ar, '');
            }
            else {
                array_push($labels_ar, '');
                array_push($values_ar, 0);
            }
        }
        $data = new \StdClass;

        $data->labels = $labels_ar;
        $data->values = $values_ar;

        return json_encode($data);
    }



    public function recoverAccount(Request $request)
    {
        $cpf = $request->sCpf;
        $cpf = substr($cpf, 0, 3) . "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);
        $pessoa = DB::table('pessoa')
            ->select('pessoa.email', 'pessoa.id')
            ->where('lixeira', 0)
            ->where('cpf_cnpj', $cpf)
            ->join('usersApp', 'usersApp.id_pessoa', 'pessoa.id')
            ->first();

        $data = new \StdClass;
        if ($pessoa) {
            if ($pessoa->email != '') {
                $email = $pessoa->email;
                $id = $pessoa->id;
                Mail::send('mail.recoverPassword', ['pessoa' => $id], function ($m) use ($email) {
                    $m->subject('Recuperação de senha - ON');
                    $m->from('recepcaomorumbi122@gmail.com');
                    $m->to($email);
                });
                $data->status = 200;
                $data->message = 'Realizamos uma verificação em nossa base de dados e enviamos por e-mail um link para recuperar sua senha.';

            } else {
                $data->status = 400;
                $data->message = "Não existe e-mail cadastrado para enviar link de recuperação";
            }
        } else {
            $data->status = 400;
            $data->message = "Esse CPF não está cadastrado em nossa base de dados! Verifique novamente.";
        }
        return json_encode($data);
    }

    public function recoverAccount2(Request $request)
    {
        $pessoa = DB::table('pessoa')
            ->select('pessoa.email', 'pessoa.id')
            ->where('lixeira', 0)
            ->where('pessoa.id', $request->id)
            ->first();

        $data = new \StdClass;
        if ($pessoa) {
            if ($pessoa->email != '') {
                $email = $pessoa->email;
                $id = $pessoa->id;
                Mail::send('mail.recoverPassword', ['pessoa' => $id], function ($m) use ($email) {
                    $m->subject('Recuperação de senha - ON');
                    $m->from('recepcaomorumbi122@gmail.com');
                    $m->to($email);
                });
                $data->sucess = 'true';
                $data->email = $pessoa->email;
            } else {
                $data->sucess = 'false';
                $data->message = "Não existe e-mail cadastrado para enviar link de recuperação";
            }
        }
        return json_encode($data);
    }

    public function return_view_recover(Request $request)
    {
        $id_pessoa = $request->id_pessoa;

        return view('recuperar_senha_app', compact('id_pessoa'));
    }

    public function savePassApp(Request $request)
    {
        $aux = DB::table('usersApp')
            ->where('id_pessoa', $request->id_pessoa)
            ->first();

        if ($aux) {
            $user = UsersApp::find($aux->id);
            $user->senha = $request->senha;
            $user->save();

            return "true";
        } else {
            $user = new UsersApp;
            $user->id_emp = 1;
            $user->id_pessoa = $request->id_pessoa;
            $user->email = Pessoa::find($request->id_pessoa)->email;
            $user->senha = $request->senha;
            $user->save();
        }
    }

    public function tela_de_sucesso()
    {
        return view('tela_de_sucesso');
    }

    public function setImagePerson(Request $request)
    {
        if ($request->imageBase64 != '') {
            $image = base64_decode($request->imageBase64);
            file_put_contents(public_path('img/pessoa') . '/' . $request->id . '.jpg', $image);
            return 'true';
        } else {
            return 'false';
        }

    }

    public function uploadDocs(Request $request)
    {
        $doc = base64_decode($request->docBase64);
        file_put_contents(public_path('anexos') . '/' . $request->docName, $doc);
        if ($request->docBase64 != '') {
            $anexo = new Anexos;
            $anexo->id_emp = Pessoa::find($request->id)->id_emp;
            $anexo->id_paciente = $request->id;
            $anexo->id_profissional = 0;
            $anexo->obs = $request->title;
            $anexo->created_at = date('Y-m-d H:i:s');
            $anexo->updated_at = date('Y-m-d H:i:s');
            $anexo->titulo = $request->docName;
            $anexo->save();
            return json_encode($anexo);
        }
        else {
            return json_encode(["response"=>"false"]);
        }
    }

    public function confirmar(Request $request) {
        DB::statement("UPDATE agenda SET travar = 1 WHERE id = ".$request->id);
    }

    public function salvar_confirmacao_agendamento(Request $request)
    {
        $agendamento = Agenda::find($request->id);

        switch ($request->decisao) {
            case 'presente':
                $agendamento->id_confirmacao = 1;
                break;
            case 'ausente':
                $agendamento->id_confirmacao = 2;
                break;
            case 'finalizado':
                $agendamento->id_confirmacao = 3;
        }
        $agendamento->save();
        return 'true';
    }









    public function testSegundoPlano(){
        $data = new ZEnvia;
        $data->id_agendamento = 0;
        $data->text = 0;
        $data->direction = "..";
        $data->celular = 0;
        $data->selected = 1;
        $data->save();
    }
}