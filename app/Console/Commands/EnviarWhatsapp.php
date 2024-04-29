<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\ControllersWebhookApiController;
use DB;
use Auth;
use App\Pessoa;
use App\Pedido;
use App\Contrato;
use App\Agenda;
use App\ContratoDados;
use App\ContratoSignatarios;
use App\Notificacao;
use App\TesteAPI;
use App\Procedimento;
use App\ZEnvia;
use GuzzleHttp\Client;
use App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class EnviarWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envio:EnvioWhatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        // $agendamentos =  DB::select(
        //     DB::raw("
        //         select
        //             *
        //         from
        //             agenda
        //         where
        //             CONVERT(CONCAT(agenda.data, ' ', SUBSTRING(agenda.hora, 1,2), ':00:00'),DATETIME) = '".date('Y-m-d H:i:s', strtotime(date('Y-m-d H').':00:00 + 1 day'))."' AND
        //             agenda.lixeira = 0 AND
        //             agenda.status = 'A' 
        //     ")
        // );

        // foreach($agendamentos AS $agendamento) {
        //     $associado = Pessoa::find($agendamento->id_paciente);
        //     if ($associado->celular1 != '' && $agendamento->id_paciente == 28480001112) {
        //         $client = new Client();

        //         $pessoa = "*". strtoupper($associado->nome_fantasia) ."*";
        //         $data_hora = "*". $agendamento->data . '* às *' . $agendamento->hora."*";
        //         $modalidade = "*". strtoupper(Procedimento::find($agendamento->id_modalidade)->descr) ."*";
        //         $membro = "*".strtoupper(Pessoa::find($agendamento->id_profissional)->nome_fantasia) ."*";
        //         $endereco = "*". strtoupper(Empresa::find($agendamento->id_emp)) ."*";

        //         $celular = $associado->celular1;
        //         $celular = str_replace('(', '', $celular);
        //         $celular = str_replace(')', '', $celular);
        //         $celular = str_replace(' ', '', $celular);
        //         $celular = str_replace('-', '', $celular);
        //         $celular = "55" . $celular;

        //         // return $celular;
        //         // $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
        //         //     "headers"=>[ 
        //         //         "Content-Type"=>"application/json",
        //         //         "X-API-TOKEN"=>"UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
        //         //     ],
        //         //     "body"=>json_encode([ 
        //         //         "from"=>"55119935248881",
        //         //         "to"=>$celular,
        //         //         "contents"=>[
        //         //             [
        //         //                 "type"=>"text",
        //         //                 "text"=>'teste'
        //         //             ]
        //         //         ]
        //         //     ])
        //         // ]);

        //         $data = Agenda::find($agendamento->id);
        //         $data->notificado = 'S';
        //         $data->save();
                
        //         $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
        //             "headers"=>[ 
        //                 "Content-Type"=>"application/json",
        //                 "X-API-TOKEN"=>"18298905-0872-4aca-8fae-436d85367126"
        //             ],
        //             "body"=>json_encode([ 
        //                 "from"=>"55119935248881",
        //                 "to"=>"5518996359414",
        //                 "contents"=>[
        //                     [
        //                         "type"=>"template",
        //                         "templateId"=>"edac0d4a-f3f4-4684-9ae1-f07461f87e37",
        //                         "fields"=>[
        //                             "associado"=> $pessoa,
        //                             "data_hora"=> $data_hora,
        //                             "modalidade"=> $modalidade,
        //                             "membro"=> $membro,
        //                             "endereco"=> $endereco,
        //                             "empresa"=> $empresa
        //                         ]
        //                     ]
        //                 ]
        //             ])
        //         ]); 

        //         $data = new ZEnvia;
        //     }
        // }
    }
}
