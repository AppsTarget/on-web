<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Pessoa;
use App\Pedido;
use App\Contrato;
use App\ContratoDados;
use App\ContratoSignatarios;
use App\Notificacao;
use App\Empresa;
use App\Agenda;
use App\ZEnvia;
use App\TesteAPI;
use App\Procedimento;
use GuzzleHttp\Client;
use App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebhookApiController extends Controller
{
    public function __construct()
    {
    }

    public function webhook(Request $request) {
        // return $request->external_id;
        $aux = DB::table('contrato')
               ->where('id_pedido', $request->external_id)
               ->first();
        $pedido = Pedido::find($aux->id_pedido);
        if ($request->event_type == "doc_signed" && $pedido && $aux){
            $contrato = Contrato::find($aux->id);
            if ($contrato->status != $request->status){
                $contrato->status         = $request->status;
                $contrato->signed_file    = $request->signed_file;
                $contrato->save();

                $pedido->assinado = 'S';
                $pedido->save();

                // CRIAR NOTIFICACAO \\
                $notificacao = new Notificacao;
                $notificacao->id_emp = Pessoa::find($pedido->id_paciente)->id_emp;
                $notificacao->id_paciente = $pedido->id_paciente;
                $notificacao->assunto = "Contrato criado em: ". date("d/m/Y", strtotime($pedido->data)). " foi assinado!";
                $notificacao->notificacao = "Contrato criado em: ". date("d/m/Y", strtotime($pedido->data)). " foi assinado!\n Link documento assinado:<a href='". $request->signed_file. "'>Clique aqui</a>";
                $notificacao->publico = false;
                $notificacao->id_profissional = $pedido->id_prof_exa;
                $notificacao->created_by = $pedido->id_prof_exa;
                $notificacao->lixeira = false;
                $notificacao->save();
                
                return $notificacao;
            }
            
        }
    }


    public function zenvia(Request $request) {
        // $data = new TesteAPI;
        
        
        $lastmsg = DB::select(
            DB::raw("
                select 
                    zenvia.id_agendamento 
                from 
                    zenvia
                    left join agenda on agenda.id = zenvia.id_agendamento 
                where 
                    agenda.lixeira = 0 AND
                    agenda.status not in ('F', 'C') AND
                    zenvia.celular = '". $request->message['from'] ."'
                order by
                    zenvia.id DESC

            ")
        );
        if (sizeof($lastmsg) == 0) return;
        // if (!in_array(Agenda::find($lastmsg[0]->id_agendamento)->id_profissional, array(
        //     1483000000,
        //     1476000000,
        //     447000000,
        //     1483000000,
        //     444000000
        // ))) {
        //     return;
        // }

        if (sizeof($lastmsg) > 0) {
            $data = new ZEnvia;
            $data->id_agendamento = $lastmsg[0]->id_agendamento;
            $data->text = $request->message['contents'][0]['text'];
            $data->direction = 'IN';
            $data->celular = $request->message['from'];
            $data->save();

            $agendamento = Agenda::find($data->id_agendamento);
            // if (sizeof($lastmsg) > 0) {
                if ($agendamento->notificado == '2') {
                    $msg = 'Ação não permitida, entre em contrato com a recepção';
                }
                else {
                    if ($data->text == '1'){
                        $agendamento->id_confirmacao = 1;
                        $status = "CONFIRMADO";
                        $msg = "Presença confirmada com sucesso";
                    }
                    else if ($data->text == '2'){
                        $agendamento->id_confirmacao = 2;
                        $status = "CANCELADO";
                        $msg = "Agendamento cancelado com sucesso! entre em contato com a recepção para remarcar";
                    }
                    else {
                        $msg = 'Comando não reconhecido';
                    }
                    $agendamento->notificado = '2';
                    $agendamento->save();
                }
            // }
            // else {
            //     $msg = 'Não existem agendamentos editáveis via whatsapp para você, consulte o app para mais informações';
            // }
        }
        // $data->text = json_encode($lastmsg[0]->id_agendamento);
        // $data->save();

        // $msg = $lastmsg[0]->id_agendamento;

        

        $client = new Client();
        $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
            "headers"=>[ 
                "Content-Type"=>"application/json",
                "X-API-TOKEN"=>"UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
            ],
            "body"=>json_encode([ 
                "from"=>"55119935248881",
                "to"=>$request->message['from'],
                "contents"=>[
                    [
                        "type"=>"text",
                        "text"=>$msg
                    ]
                ]
            ])
        ]); 
        $response =  json_decode(strval($response->getBody()));
        if ($agendamento->notificado == '2') return json_encode($request);

        $celular = Pessoa::find($agendamento->id_profissional)->celular1;
        $celular = str_replace('(', '', $celular);
        $celular = str_replace(')', '', $celular);
        $celular = str_replace(' ', '', $celular);
        $celular = str_replace('-', '', $celular);
        $celular = "55" . $celular;

        $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
            "headers"=>[ 
                "Content-Type"=>"application/json",
                "X-API-TOKEN"=>"UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
            ],
            "body"=>json_encode([ 
                "from"=>"55119935248881",
                "to"=>$celular,
                "contents"=>[
                    [
                        "type"=>"template",
                        "templateId"=>"2d8c674f-9297-4e90-ace9-9841c205e814",
                        "fields"=>[
                            "membro"=> Pessoa::find($agendamento->id_profissional)->nome_fantasia,
                            "data"=> date('d/m/Y', strtotime($agendamento->data)),
                            "hora"=>     substr($agendamento->hora, 0, 5),
                            "status"=>    $status,
                            "associado"=> Pessoa::find($agendamento->id_paciente)->nome_fantasia
                        ]
                    ]
                ]
            ])
        ]); 
        // return response(true, 200);
        return json_encode($request);
    }






    public function enviar_mensagem(Request $request) {
        $agendamentos =  DB::select(
            DB::raw("
                select
                    *
                from
                    agenda
                where
                    agenda.data = '2023-03-23' AND agenda.hora > '08:00:00' AND
                    agenda.lixeira = 0 AND
                    agenda.status = 'A' 
            ")
        );
        // return $agendamentos;$request->message['contents'][0]['text'];

        // return $agendamentos;
        foreach($agendamentos AS $agendamento) {
            // return Empresa::find($agendamento->id_emp)->descr;
            $associado = Pessoa::find($agendamento->id_paciente);
            if ($associado->celular1 != '') {
                $client = new Client();

                $pessoa = "*". strtoupper($associado->nome_fantasia) ."*";
                $data_hora = "*". $agendamento->data . '* às *' . $agendamento->hora."*";
                $modalidade = "*". strtoupper(Procedimento::find($agendamento->id_modalidade)->descr) ."*";
                $membro = "*".strtoupper(Pessoa::find($agendamento->id_profissional)->nome_fantasia) ."*";
                $endereco = "*". strtoupper(Empresa::find($agendamento->id_emp)) ."*";
                $empresa = Empresa::find($agendamento->id_emp)->descr;

                $celular = $associado->celular1;
                $celular = str_replace('(', '', $celular);
                $celular = str_replace(')', '', $celular);
                $celular = str_replace(' ', '', $celular);
                $celular = str_replace('-', '', $celular);
                $celular = "55" . $celular;

                // return $celular;
                // $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
                //     "headers"=>[ 
                //         "Content-Type"=>"application/json",
                //         "X-API-TOKEN"=>"UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
                //     ],
                //     "body"=>json_encode([ 
                //         "from"=>"55119935248881",
                //         "to"=>$celular,
                //         "contents"=>[
                //             [
                //                 "type"=>"text",
                //                 "text"=>'teste'
                //             ]
                //         ]
                //     ])
                // ]);

                
                
                if($agendamento->notificado == 'N') {
                    $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
                        "headers"=>[ 
                            "Content-Type"=>"application/json",
                            "X-API-TOKEN"=>"UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
                        ],
                        "body"=>json_encode([ 
                            "from"=>"55119935248881",
                            "to"=>$celular,
                            "contents"=>[
                                [
                                    "type"=>"template",
                                    "templateId"=>"edac0d4a-f3f4-4684-9ae1-f07461f87e37",
                                    "fields"=>[
                                        "associado"=> $pessoa,
                                        "data_hora"=> $data_hora,
                                        "modalidade"=> $modalidade,
                                        "membro"=> $membro,
                                        "endereco"=> $endereco,
                                        "empresa"=> $empresa
                                    ]
                                ]
                            ]
                        ])
                    ]); 
                    $data = Agenda::find($agendamento->id);
                    $data->notificado = 'S';
                    $data->save();

                    $data = new ZEnvia;
                    $data->id_agendamento = $agendamento->id;
                    $data->text = "";
                    $data->direction = "OUT";
                    $data->celular = $celular;
                    $data->selected = 'S';
                    $data->save();
                }
            }
        }
    }
}