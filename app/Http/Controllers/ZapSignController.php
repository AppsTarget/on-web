<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Pessoa;
use App\Pedido;
use App\Contrato;
use App\ContratoDados;
use App\ContratoSignatarios;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
Use App\Mail\contratoEmail;
use Illuminate\Support\Facades\Mail;

class ZapSignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cadastrar_signatario($id_contrato) {
        $pedido = Pedido::find($id_contrato);
        $pessoa = Pessoa::find($pedido->id_paciente);
        
        $aux = DB::table('contrato')->where('id_pedido', $id_contrato)->first();
        // if ($aux) return $aux;

        $client = new Client();

        // MONTAR ENDERECO \\
        if ($pessoa->complemento != null && $pessoa->complemento != 'null') $complemento = ", ". $pessoa->complemento . ", ";
        else                                                                $complemento = "";
        $endereco = $pessoa->endereco . ', N. ' . $pessoa->numero . $complemento . " - Bairro: " . $pessoa->bairro . " - CEP: " . $pessoa->cep . " - Cidade: ". $pessoa->cidade . " - " . $pessoa->uf;
        
        // MONTAR MES \\
        switch(intval(date("m", strtotime($pedido->data)))) {
            case 1:
                $mes = "Janeiro";
                break;
            case 2:
                $mes = "Fevereiro";
                break;
            case 3:
                $mes = "Março";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Maio";
                break;
            case 6:
                $mes = "Junho";
                break;
            case 7:
                $mes = "Julho";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Setembro";
                break;
            case 10:
                $mes = "Outubro";
                break;
            case 11:
                $mes = "Novembro";
                break;
            case 12:
                $mes = "Dezembro";
                break;
        }
        
        // MONTAR FORMA DE PAGAMENTO \\
        $aux_forma_pag = DB::table('pedido_forma_pag')
                        ->select('forma_pag.descr',
                                 'pedido_forma_pag.num_total_parcela as total_parcelas',
                                 'pedido_forma_pag.valor_total')
                        ->leftjoin('forma_pag', 'forma_pag.id', 'pedido_forma_pag.id_forma_pag')
                        ->where('pedido_forma_pag.id_pedido', $id_contrato)
                        ->get();
        $formas_de_pagamento = '';
        foreach($aux_forma_pag As $forma_pag) {
            if ($formas_de_pagamento != '') $formas_de_pagamento .= ', ';
            $formas_de_pagamento .= $forma_pag->descr;
            $formas_de_pagamento .= ' em ' . $forma_pag->total_parcelas;

            $valor_auxiliar = ($forma_pag->valor_total / $forma_pag->total_parcelas);
            $valor_auxiliar = number_format($valor_auxiliar,2,",",".");
            $formas_de_pagamento .= 'x de R$ ' . $valor_auxiliar;
        }
        
        
        // MONTAR PLANOS \\
        $bsessao  = false;
        $bfull    = false;
        $blight   = false;
        $bmodelo  = false;

        $planos_ar = DB::table('pedido_planos')
                  ->select('tabela_precos.descr', 'tabela_precos.id', 'tabela_precos.vigencia')
                  ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                  ->where('pedido_planos.id_pedido', $pedido->id)
                  ->get();
        
        $planos = '';
        foreach($planos_ar AS $plano) {
            switch(intval($plano->vigencia)) {
                case 30:
                    $vigencia = "mensal";
                    break;
                case 60:
                    $vigencia = "bimestral";
                    break;
                case 90:
                    $vigencia = "trimestral";
                    break;
                case 180:
                    $vigencia = "semestral";
                    break;
                default:
                    $vigencia = "anual";
            }
            if ($plano->id != $planos_ar[0]->id) $planos .= "\n";
            $planos .= "- " . $plano->descr . " (" . $vigencia . ")\n";
        }

        
        $response = $client->request('POST', ZapSignApi() . 'models/create-doc/?api_token='. ZapSignToken(), [
                                                        "body"=>json_encode([ 
                                                        "sandbox"=>false,
                                                        "template_id"=>"8c6d1007-781a-425f-8aff-c17bd3cf5d48",
                                                        "signer_name"=>$pessoa->nome_fantasia,
                                                        "signer_email"=>$pessoa->email,
                                                        "signer_phone_country"=>55,
                                                        "signer_phone_number"=>$pessoa->celular1,
                                                        "external_id"=>$pedido->id,
                                                        "send_automatic_whatsapp"=> false,
                                                        "send_automatic_email" => false,
                                                        "data"=>[
                                                            [
                                                                "de"=> "{{ descr_emp }}",
                                                                "para"=>getEmpresaObj()->descr
                                                            ],
                                                            [
                                                                "de"=>"{{ end_emp }}",
                                                                "para"=>getEmpresaObj()->endereco
                                                            ],
                                                            [
                                                                "de"=>"{{ nome_contratante }}",
                                                                "para"=>$pessoa->nome_fantasia
                                                            ],
                                                            [
                                                                "de"=>"{{ cpf_contratante }}",
                                                                "para"=>$pessoa->cpf_cnpj
                                                            ],
                                                            [
                                                                "de"=>"{{ rg_contratante }}",
                                                                "para"=>$pessoa->rg_ie
                                                            ],
                                                            [
                                                                "de"=>"{{ endereco_contratante }}",
                                                                "para"=>$endereco
                                                            ],
                                                            [
                                                                "de"=>"{{ planos }}",
                                                                "para"=>$planos
                                                            ],
                                                            [
                                                                "de"=>"{{ forma_pag }}",
                                                                "para"=>$formas_de_pagamento
                                                            ],
                                                            [
                                                                "de"=>"{{ dia_contratacao }}",
                                                                "para"=>date("Y", strtotime($pedido->data))
                                                            ],
                                                            [
                                                                "de"=>"{{ mes_contratacao }}",
                                                                "para"=>$mes
                                                            ],
                                                            [
                                                                "de"=>"{{ ano_contratacao }}",
                                                                "para"=>date("Y", strtotime($pedido->data))
                                                            ]
                                                        ]
                                                    ])]); 
        $response =  json_decode(strval($response->getBody()));
            
        // $response = json_decode('{"sandbox":true,"external_id":"1980","open_id":3,"token":"2e199162-2930-4fd5-95d4-551d62c6ffab","name":"Contrato de Venda","folder_path":"/","status":"pending","lang":"pt-br","original_file":"https://zapsign.s3.amazonaws.com/2022/12/pdf/81298b2b-cf7a-4277-b884-8a1ee1c70074/ac4e122c-a0c2-4274-b338-d5d20d7cb2bf.pdf","signed_file":null,"extra_docs":[],"created_through":"api-template","deleted":false,"deleted_at":null,"signed_file_only_finished":false,"disable_signer_emails":false,"brand_logo":"","brand_primary_color":"","created_at":"2022-12-04T06:41:31.938615Z","last_update_at":"2022-12-04T06:41:31.938636Z","created_by":{"email":"onevolucaocorporal2017@gmail.com"},"template":{"token":"c3ac6de4-f745-447e-8940-668471024062"},"signers":[{"external_id":"","sign_url":"https://app.zapsign.com.br/verificar/81123459-54f9-4198-990f-753c5fe5c2ad","token":"81123459-54f9-4198-990f-753c5fe5c2ad","status":"new","name":"Vinicius Cavani Behlau","lock_name":true,"email":"vinicavani123@gmail.com","lock_email":true,"hide_email":false,"blank_email":false,"phone_country":"55","phone_number":"11989118800","lock_phone":true,"hide_phone":false,"blank_phone":false,"times_viewed":0,"last_view_at":null,"signed_at":null,"auth_mode":"assinaturaTela","qualification":"","require_selfie_photo":false,"require_document_photo":false,"geo_latitude":null,"geo_longitude":null,"redirect_link":"","signature_image":null,"visto_image":null,"document_photo_url":"","document_verse_photo_url":"","selfie_photo_url":"","selfie_photo_url2":"","send_via":"email"}],"answers":[{"variable":" ano_contratacao }","value":"2022"},{"variable":" mes_contratacao }","value":"Dezembro"},{"variable":" dia_contratacao }","value":"2022"},{"variable":" forma_pag }","value":"CARTÃO DE CRÉDITO"},{"variable":" planos }","value":"- 2X (LIGHT), PLANO COM ACESSO PERMITIDO POR 2 (DOIS) DIAS SEMANAIS, DIREITO A 8 SESSÕES MENSAIS DE HABILITAÇÃO (LPO, YOGA, PILATES E PREPARAÇÃO FÍSICA), CONFORME TRIAGEM DO IEC (ÍNDICE DE EVOLUÇÃO CORPORAL) E PROFISSIONAIS DA ON (VIGENCIA: 180 DIAS, VÁLIDO ATÉ\n"},{"variable":" endereco_contratante }","value":"Rua Doutor Teixeira Camargo, N. 403, até 520/521,  - Bairro: Vila Operária - CEP: 19804-000 - Cidade: Assis - SP"},{"variable":" rg_contratante }","value":"55.481.576-x"},{"variable":" cpf_contratante }","value":"510.775.938-64"},{"variable":" nome_contratante ","value":"Vinicius Cavani Behlau"},{"variable":" end_emp ","value":""},{"variable":" descr_emp ","value":"ON - EVOLUÇÃO CORPORAL - MORUMBI"}],"auto_reminder":0}');

        
        
        $contrato = new Contrato;

        $contrato->id_emp           = getEmpresa();
        $contrato->open_id          = $response->open_id;
        $contrato->id_pedido        = $response->external_id;
        $contrato->token            = $response->token;
        $contrato->status           = $response->status;
        $contrato->name             = $response->name;
        $contrato->original_file    = $response->original_file;
        $contrato->signed_file      = $response->signed_file;
        $contrato->updated_at_ext   = date('Y-m-d : H:i:s', strtotime($response->last_update_at));
        $contrato->created_by       = Auth::user()->id_profissional;
        $contrato->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
        $contrato->save();

        foreach($response->signers AS $signatario) {
            $contrato_signatarios = new ContratoSignatarios;

            $contrato_signatarios->token          = $signatario->token;
            $contrato_signatarios->id_contrato    = $contrato->id;
            $contrato_signatarios->sign_url       = $signatario->sign_url;
            $contrato_signatarios->status         = $signatario->status;
            $contrato_signatarios->name           = $signatario->name;
            $contrato_signatarios->email          = $signatario->email;
            $contrato_signatarios->phone_country  = $signatario->phone_country;
            $contrato_signatarios->phone_number   = $signatario->phone_number;
            $contrato_signatarios->times_viewed   = $signatario->times_viewed;
            $contrato_signatarios->last_viewed_at = date('Y-m-d : H:i:s', strtotime($response->last_update_at));
            $contrato_signatarios->save();

            $response2 = $client->request('POST', ZapSignApi() . 'signers/'. $signatario->token .'/?api_token='. ZapSignToken(), [
                "body"=>json_encode([ 
                "redirect_link"=>"",
                "name"=>$pessoa->nome_fantasia,
                "email"=>$pessoa->email,
                "phone_country"=>55,
                "phone_number"=>$pessoa->celular1,
                "auth_mode"=>"assinaturaTela-tokenEmail",
                "lock_name"=>true,
                "lock_email"=> true,
                "lock_phone"=> true,
                "qualification"=> "Responsável"
            ])]);
        }


        // foreach($response->answers AS $answers) {
        //     $contrato_dados = new ContratoDados;

        //     $contrato_dados->id_contrato = $contrato->id;
        //     $contrato_dados->variable    = $answers->variable;
        //     $contrato_dados->value       = $answers->value;
        //     $contrato_dados->save();
        // }

        return $contrato;
    }


    public function exibir_sucesso(Request $request) {
        // $data = new StdClass;

        
    }






    public function enviar_email(Request $request) {
        $link = DB::table('contrato_signatarios')
                ->select('contrato_signatarios.sign_url AS sign_url', 'pessoa.email')
                ->leftjoin('contrato', 'contrato.id', 'contrato_signatarios.id_contrato')
                ->leftjoin('pedido', 'pedido.id', 'contrato.id_pedido')
                ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                ->where('contrato.id_pedido', $request->id_pedido)
                ->first();
        
        $email = "Link para assinar: ". $link->sign_url;

        // Mail::send('mail.email-generico', ['email' => $email], function($m) use($link){
        //     $m->subject('Assinar contrato!');
        //     $m->from('recepcaomorumbi122@gmail.com');
        //     $m->to($link->email);
        // });
        Mail::to($link->email)->send(new ContratoEmail($email));
        return 'true';
    }
    public function enviar_email02(Request $request) {
        
        $email = "Link para assinar: ";

        Mail::send('mail.email-generico', ['email' => $email], function($m){
            $m->subject('Assinar contrato!');
            $m->from('recepcaomorumbi122@gmail.com');
            $m->to("vinicavani123@gmail.com");
        });
        return 'true';
    }


    public function enviar_whatsapp(Request $request) {
    }







    public function sincronizar_assinaturas(Request $request) {
        $client = new Client;

        // for ($i = 1; $i <= 7; $i++) {
            $response = $client->request('GET', ZapSignApi() . 'docs?api_token=' . ZapSignToken() . '&page=6', []);
            $response =  json_decode(strval($response->getBody()));

            foreach($response->results AS $resposta) {
                if ($resposta->external_id != '') {
                    $aux = DB::table('contrato')
                           ->where('id_pedido', $resposta->external_id)
                           ->get();

                    if (sizeof($aux) > 0) $contrato = Contrato::find($aux[0]->id);
                    else                  $contrato = new Contrato;

                    $contrato->id_emp           = getEmpresa();
                    $contrato->open_id          = $resposta->open_id;
                    $contrato->id_pedido        = $resposta->external_id;
                    $contrato->token            = $resposta->token;
                    $contrato->status           = $resposta->status;
                    $contrato->name             = $resposta->name;
                    $contrato->original_file    = $resposta->original_file;
                    $contrato->signed_file      = $resposta->signed_file;
                    $contrato->updated_at_ext   = date('Y-m-d : H:i:s', strtotime($resposta->last_update_at));
                    $contrato->created_by       = Auth::user()->id_profissional;
                    $contrato->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $contrato->save();
                }
            }
        // }
    }
}