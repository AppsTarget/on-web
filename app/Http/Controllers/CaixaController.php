<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Caixa;
use App\CaixaMov;
use App\CaixaOperadores;
use App\Pessoa;
use App\Evolucao;
use App\PedidoPlanos;
use App\TabelaPrecos;
use App\Procedimento;
use App\Pedido;
use App\EmpresasPlano;
use Illuminate\Http\Request;

class CaixaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $empresa = DB::table('empresa')
                   ->get();

        $caixas = DB::table('caixa')
                  ->select('caixa.id',
                           'caixa.descr',
                           'empresa.descr as empresa',
                           'caixa.h_abertura',
                           'caixa.h_fechamento',
                           'caixa.ativo')
                 ->leftjoin('empresa', 'empresa.id', 'caixa.id_emp')
                 ->where('lixeira', 0)
                 ->get();


        return view('cadastro_caixa', compact('empresa', 'caixas'));
    }

    public function salvar_cadastro(Request $request){
        if ($request->id <> 0) $caixa = Caixa::find($request->id);
        else                   $caixa = new Caixa;

        $caixa->id_emp = $request->emp;
        $caixa->descr = $request->descr;
        $caixa->ativo = $request->situacao;
        $caixa->h_abertura = $request->h_abertura;
        $caixa->h_fechamento = $request->h_fechamento;
        $caixa->save();

        DB::table('caixa_operadores')
        ->where('id_caixa', $caixa->id)
        ->delete();
        
        if ($request->operadores){
            foreach($request->operadores as $op_aux) {
                $operador = new CaixaOperadores;
                $operador->id_caixa = $caixa->id;
                $operador->id_operador = $op_aux;
                $operador->save();
            }
        }
        return 'S';
    }

    public function editar_cadastro_caixa($id){
        $caixa = DB::table('caixa')
                ->select('caixa.id AS id',
                         'caixa.descr AS descr',
                         'caixa.id_emp AS id_emp',
                         'caixa.ativo AS ativo',
                         'caixa.h_abertura AS h_inicial',
                         'caixa.h_fechamento AS h_final')
                ->where('caixa.id', $id)
                ->first();

        $operadores = DB::table('caixa_operadores')
                      ->select('pessoa.id AS id',
                               'pessoa.nome_fantasia AS nome')
                      ->leftjoin('pessoa', 'pessoa.id', 'caixa_operadores.id_operador')
                      ->where('caixa_operadores.id_caixa', $id)
                      ->get();
        
        $data = new \StdClass;
        $data->caixa = $caixa;
        $data->operadores = $operadores;

        return json_encode($data);
    }

    public function verificar_situacao(){
        $caixa = DB::table('caixa')
                 ->select('caixa.d_ult_abertura', 'caixa.situacao', 'caixa.id')
                 ->leftjoin('caixa_operadores', 'caixa_operadores.id_caixa', 'caixa.id')
                 ->where('caixa.id_emp', getEmpresa())
                 ->where('caixa_operadores.id_operador', Auth::user()->id_profissional)
                 ->where('lixeira', 0)
                 ->where('ativo', 'S')
                 ->first();
        
        // return json_encode($caixa);
        if ($caixa){
            if (strtotime($caixa->d_ult_abertura) < strtotime(date('Y-m-d'))){
                $data = new \StdClass;
                $data->situacao = $caixa->situacao;
                $data->abrir_fechar = true;
                $data->data_ult_abertura = $caixa->d_ult_abertura;
            }
            else {
                $data = new \StdClass;
                $data->situacao = $caixa->situacao;
                $data->abrir_fechar = false;
                $data->data_ult_abertura = $caixa->d_ult_abertura;
            }
            $data->id_caixa = $caixa->id;
            return json_encode($data);
        }
        else {
            $data = new \StdClass;
            $data->situacao = 'X';

            return json_encode($data);
        }
    }

    public function bloquear_cadastro_caixa(Request $request){
        $caixa = Caixa::find($request->id);
        $caixa->ativo = 'N';
        $caixa->save();
        return 'true';
    }

    public function desbloquear_cadastro_caixa(Request $request) {
        $caixa = Caixa::find($request->id);
        $caixa->ativo = 'S';
        $caixa->save();
        return 'true';
    }
    
    public function excluir_cadastro_caixa(Request $request) {
        $caixa = Caixa::find($request->id);
        $caixa->lixeira = 1; 
        $caixa->save();
        return 'true';
    }
    

    public function abrir_modal($data_externa, $id) {
        // return 
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $id)
                    ->where('caixa_mov.data', $data_externa)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();
        // return json_enco
        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $id)
                    ->where('caixa_mov.data', $data_externa)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>',  $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        // return json_encode($T_final);
        /*if ($T_inicial) return 'verdadeiro';
        else            return 'falso';*/
        if ($T_inicial) {
            $data = new \StdClass;
            $aux_titulos = DB::table('caixa_mov')
            ->select(DB::raw('MAX(pedido_forma_pag.valor_total) AS valor_total'),
                     DB::raw('MAX(titulos_receber.valor_total_pago) AS valor_total_pago'),
                     DB::raw('MAX(pedido_forma_pag.troco) AS troco'),
                     DB::raw('MAX(pedido_forma_pag.id_forma_pag) AS id_forma_pag'),
                     'pedido.id_convenio',
                     'convenio.descr')
            ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
            ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
            ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
            ->leftjoin('titulos_receber', 'titulos_receber.id_pedido', 'pedido.id')
            ->where(function($sql) use($T_inicial, $T_final){
                if ($T_inicial){
                    if($T_final){
                        $sql->where('caixa_mov.data',">=", $T_inicial->data)
                            ->where('caixa_mov.hora',">=", $T_inicial->hora)
                            ->where('caixa_mov.data',"<=", $T_final->data)
                            ->where('caixa_mov.hora',"<=", $T_final->hora);
                    }
                    else {
                        $sql->where('caixa_mov.data', $T_inicial->data)
                            ->where('caixa_mov.hora',">=", $T_inicial->hora);
                    }
                    
                }
            })
            ->whereRaw('((pedido.id is null AND titulos_receber.id_pedido = 0) or pedido.lixeira = 0)')
            ->where('caixa_mov.id_caixa', $id)
            ->groupBy('pedido_forma_pag.valor_total',
                     'titulos_receber.valor_total_pago',
                     'pedido_forma_pag.troco',
                     'pedido_forma_pag.id_forma_pag',
                     'pedido.id_convenio',
                     'convenio.descr',
                     'caixa_mov.data',
                     'caixa_mov.hora')
            ->orderBy('caixa_mov.data')
            ->orderBy('caixa_mov.hora')
            ->get();

            $aux_saldo_inicial = DB::table('caixa_mov')
                                ->select('saldo_resultante','data', 'hora')
                                ->where(function($sql) use($T_inicial, $T_final){
                                    if ($T_inicial){
                                        if($T_final){
                                            $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                ->where('caixa_mov.data',"<=", $T_final->data)
                                                ->where('caixa_mov.hora',"<=", $T_final->hora);
                                        }
                                        else {
                                            $sql->where('caixa_mov.data', $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                        }
                                        
                                    }
                                })
                                ->where(function($sql){
                                    $sql->where('tipo', 'R')
                                        ->orWhere('tipo', 'A');
                                })
                                ->where('caixa_mov.id_caixa', $id)
                                ->where('saldo_resultante', '>', 'saldo_anterior')
                                ->orderBy('hora')
                                ->first();
    
            if ($aux_saldo_inicial) $saldo_inicial = $aux_saldo_inicial->saldo_resultante;
            else                    $saldo_inicial = getCaixa()->valor;
    
            $suprimento = DB::table('caixa_mov')
                            ->select(DB::raw("SUM(saldo_resultante - saldo_anterior) AS total"))
                            ->whereRaw("(
                                    data = '". date('Y-m-d') ."'
                                    and tipo = 'R' 
                                    and saldo_anterior < saldo_resultante
                                )")
                            ->where(function($sql) use($T_inicial, $T_final){
                                if ($T_inicial){
                                    if($T_final){
                                        $sql->where('caixa_mov.data',">=", $T_inicial->data);
                                    }
                                    else {
                                        $sql->where('caixa_mov.data', $T_inicial->data);
                                    }
                                    
                                }
                            })
                            ->where('caixa_mov.id_caixa', $id)
                            ->value('total');
            $recebimento_vista = 0;
            $recebimento_prazo = 0;
            $sangria           = DB::table('caixa_mov')
                                ->select(DB::raw("SUM(saldo_anterior - saldo_resultante) AS total"))
                                ->whereRaw("(
                                    data = '". date('Y-m-d') ."'
                                    and tipo = 'R' 
                                    and saldo_anterior > saldo_resultante
                                )")
                                ->where(function($sql) use($T_inicial, $T_final){
                                    if ($T_inicial){
                                        if($T_final){
                                            $sql->where('caixa_mov.data',">=", $T_inicial->data);
                                        }
                                        else {
                                            $sql->where('caixa_mov.data', $T_inicial->data);
                                        }
                                        
                                    }
                                })
                                ->where('caixa_mov.id_caixa', $id)
                                ->value('total');
    
            if ($sangria    == 'null' || $sangria    <= 0) $sangria = 0;
            if ($suprimento == 'null' || $suprimento <= 0) $suprimento = 0;
    
            $total_cartao = 0;
            $total_cartao_debito = 0;
            $total_cartao_credito = 0;
    
            $total_transferencia = 0;
            $valor_pix           = 0;
            $valor_transferencia = 0;
    
            $total_boleto    = 0;
            $total_duplicata = 0;
    
            $total_convenio = 0;
    
            $total_recebimentos         = 0;
            $total_vendas               = 0;
            $total_sangrias_suprimentos = 0;
    
            foreach($aux_titulos AS $titulo){
                if ($titulo->valor_total > 0) {
                    // DINHEIRO \\
                    if ($titulo->id_forma_pag == 2) {
                        // $total_entrada_dinheiro += $titulo->valor_total;
                        // $total_saida_dinheiro   += $titulo->troco;
                        $total_recebimentos     += $titulo->valor_total;
                        $total_vendas           += $titulo->valor_total - $titulo->troco;
                        
                    }
    
                    // CARTAO \\
                    if ($titulo->id_forma_pag == 1 || $titulo->id_forma_pag == 6) {
                        $total_cartao         += $titulo->valor_total;
                        $total_cartao_credito += $titulo->valor_total;
                        $total_recebimentos   += $titulo->valor_total;
                        $total_vendas         += $titulo->valor_total;
                    }
                    if ($titulo->id_forma_pag == 3) {
                        $total_cartao        += $titulo->valor_total;
                        $total_cartao_debito += $titulo->valor_total;
                        $total_recebimentos  += $titulo->valor_total;
                        $total_vendas        += $titulo->valor_total;
                    }
    
                    // TRANFERENCIA \\
                    if($titulo->id_forma_pag == 4) {
                        $total_transferencia += $titulo->valor_total;
                        $valor_pix           += $titulo->valor_total;
                        $total_recebimentos  += $titulo->valor_total;
                        $total_vendas        += $titulo->valor_total;
                        $recebimento_vista      += $titulo->valor_total = $titulo->valor_total - $titulo->troco;
                    }
                    if($titulo->id_forma_pag == 5) {
                        $total_transferencia += $titulo->valor_total;
                        $valor_transferencia += $titulo->valor_total;
                        $total_recebimentos  += $titulo->valor_total;
                        $total_vendas        += $titulo->valor_total;
                        $recebimento_vista      += $titulo->valor_total = $titulo->valor_total - $titulo->troco;
                    }
                    if ($titulo->id_forma_pag == 10 || $titulo->id_forma_pag == 7) $total_vendas += $titulo->valor_total;
                    
    
                    // // BOLETO \\
                    // if($titulo->id_forma_pag == 7) {
                    //     $total_boleto += $titulo->valor_total;
                    // }
    
                    // // DUPLICATA \\
                    // if($titulo->id_forma_pag == 10) {
                    //     $total_duplicata += $titulo->valor_total;
                    // }
                }
            }
    
            $data->id_caixa = getCaixa()->id;
    
            // DINHEIRO \\
            // $data->saldo_caixa        = getCaixa()->valor;
            $data->saldo_caixa = DB::table('caixa_mov')
                                ->where(function($sql) use($T_inicial, $T_final){
                                    if ($T_inicial){
                                        if($T_final){
                                            $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                ->where('caixa_mov.data',"<=", $T_final->data)
                                                ->where('caixa_mov.hora',"<=", $T_final->hora);
                                        }
                                        else {
                                            $sql->where('caixa_mov.data', $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                        }
                                        
                                    }
                                })
                                ->where('caixa_mov.id_caixa', getCaixa()->id)
                                ->orderBy('id', 'DESC')
                                ->value('saldo_resultante');
            $data->saldo_inicial      = $saldo_inicial; 
            $data->suprimento         = $suprimento; 
            $data->recebimento_vista  = DB::table('caixa_mov')
                                        ->select(DB::raw('SUM(pedido_forma_pag.valor_total) AS total'))
                                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                        ->leftjoin('pedido_forma_pag', function($join) {
                                            $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                            $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                        })
                                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                        ->whereRaw("(pedido_forma_pag.id_forma_pag in (1, 3) AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                                        ->where(function($sql) use($T_inicial, $T_final){
                                            if ($T_inicial){
                                                if($T_final){
                                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                                }
                                                else {
                                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                                }
                                                
                                            }
                                        })
                                        ->where('pedido_forma_pag.id_forma_pag', 2)
                                        ->where('caixa_mov.id_caixa', $id)
                                        ->orderBy('caixa_mov.data')
                                        ->orderBy('caixa_mov.hora')
                                        ->value('total');
                               
            if (!$data->recebimento_vista) $data->recebimento_vista = 0;                                            
            $data->recebimento_prazo  = $recebimento_prazo; 
            $data->sangria            = $sangria; 
    
            // CARTAO \\
            $data->total_cartao = DB::table('caixa_mov')
                                ->select(DB::raw('SUM(pedido_forma_pag.valor_total) AS total'))
                                ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                ->leftjoin('pedido_forma_pag', function($join) {
                                    $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                    $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                })
                                ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                ->whereRaw("(pedido_forma_pag.id_forma_pag in (1, 3) AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                                ->where(function($sql) use($T_inicial, $T_final){
                                    if ($T_inicial){
                                        if($T_final){
                                            $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                ->where('caixa_mov.data',"<=", $T_final->data)
                                                ->where('caixa_mov.hora',"<=", $T_final->hora);
                                        }
                                        else {
                                            $sql->where('caixa_mov.data', $T_inicial->data)
                                                ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                        }
                                        
                                    }
                                })
                                ->where('caixa_mov.id_caixa', $id)
                                ->orderBy('caixa_mov.data')
                                ->orderBy('caixa_mov.hora')
                                ->value('total');
            if ($data->total_cartao == 'null' || $data->total_cartao == null){
                $data->total_cartao = 0;
            }
            $data->total_cartao_debito  = DB::table('caixa_mov')
                                            ->select(DB::raw('SUM(pedido_forma_pag.valor_total) AS total'))
                                            ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                            ->leftjoin('pedido_forma_pag', function($join) {
                                                $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                                $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                            })
                                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                            ->whereRaw("(pedido_forma_pag.id_forma_pag = 3 AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                                            ->where(function($sql) use($T_inicial, $T_final){
                                                if ($T_inicial){
                                                    if($T_final){
                                                        $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                            ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                            ->where('caixa_mov.data',"<=", $T_final->data)
                                                            ->where('caixa_mov.hora',"<=", $T_final->hora);
                                                    }
                                                    else {
                                                        $sql->where('caixa_mov.data', $T_inicial->data)
                                                            ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                                    }
                                                    
                                                }
                                            })
                                            ->where('caixa_mov.id_caixa', $id)
                                            ->orderBy('caixa_mov.data')
                                            ->orderBy('caixa_mov.hora')
                                            ->value('total');
            if ($data->total_cartao_debito == 'null' || $data->total_cartao_debito == null){
                $data->total_cartao_debito = 0;
            }
            $data->total_cartao_credito = DB::table('caixa_mov')
                                            ->select(DB::raw('SUM(pedido_forma_pag.valor_total) AS total'))
                                            ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                            ->leftjoin('pedido_forma_pag', function($join) {
                                                $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                                $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                            })
                                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                            ->whereRaw("(pedido_forma_pag.id_forma_pag = 1 AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                                            ->where(function($sql) use($T_inicial, $T_final){
                                                if ($T_inicial){
                                                    if($T_final){
                                                        $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                            ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                            ->where('caixa_mov.data',"<=", $T_final->data)
                                                            ->where('caixa_mov.hora',"<=", $T_final->hora);
                                                    }
                                                    else {
                                                        $sql->where('caixa_mov.data', $T_inicial->data)
                                                            ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                                    }
                                                    
                                                }
                                            })
                                            ->where('caixa_mov.id_caixa', $id)
                                            ->orderBy('caixa_mov.data')
                                            ->orderBy('caixa_mov.hora')
                                            ->value('total');
            if ($data->total_cartao_credito == 'null' || $data->total_cartao_credito == null){
                $data->total_cartao_credito = 0;
            }
            // TRANSFERENCIA \\
            $data->total_transferencia = $total_transferencia;
            $data->valor_pix           = $valor_pix;
            $data->valor_transferencia = $valor_transferencia;
    
            // BOLETO \\
            $data->total_boleto    = $total_boleto;
            // DUPLICATA \\
            $data->total_duplicata = $total_duplicata;
            // CONVENIO \\
            $data->total_convenio  =  DB::table('caixa_mov')
                                        ->select(DB::raw('SUM(caixa_mov.valor) AS total'))
                                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                        ->leftjoin('pedido_forma_pag', function($join) {
                                            $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                            $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                        })
                                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                        ->whereRaw("(pedido.id_convenio <> 0 and (pedido.lixeira = 0 or pedido.lixeira is null))")
                                        ->where(function($sql) use($T_inicial, $T_final){
                                            if ($T_inicial){
                                                if($T_final){
                                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                                }
                                                else {
                                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                                }
                                                
                                            }
                                        })
                                        ->where('caixa_mov.id_caixa', $id)
                                        ->orderBy('caixa_mov.data')
                                        ->orderBy('caixa_mov.hora')
                                        ->value('total');
            if ($data->total_convenio == 'null' || $data->total_convenio == null){
                $data->total_convenio = 0;
            }                                        
            $data->lista_convenios = DB::table('caixa_mov')
                                    ->select(DB::raw('SUM(pedido_forma_pag.valor_total) AS valor_total'),
                                            'pedido.id_convenio',
                                            'convenio.descr')
                                    ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                    ->leftjoin('pedido_forma_pag', function($join) {
                                        $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                                        $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                                    })
                                    ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                    ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                                    ->whereRaw("(pedido.id_convenio <> 0 and (pedido.lixeira = 0 or pedido.lixeira is null))")
                                    ->where(function($sql) use($T_inicial, $T_final){
                                        if ($T_inicial){
                                            if($T_final){
                                                $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                    ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                    ->where('caixa_mov.data',"<=", $T_final->data)
                                                    ->where('caixa_mov.hora',"<=", $T_final->hora);
                                            }
                                            else {
                                                $sql->where('caixa_mov.data', $T_inicial->data)
                                                    ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                            }
                                            
                                        }
                                    })
                                    ->where('caixa_mov.id_caixa', $id)
                            ->groupBy('pedido.id_convenio', 'convenio.descr')
                            ->get();
            $total_suprimentos = 0;
            $total_sangria = 0;
            
            $aux_suprimentos = DB::table('caixa_mov')
                        ->where('tipo', 'R')
                        ->get();
            foreach($aux_suprimentos AS $mov) {
                if ($mov->saldo_anterior > $mov->saldo_resultante){
                    $total_sangria              += $mov->saldo_anterior - $mov->saldo_resultante;
                    $total_sangrias_suprimentos += $mov->saldo_anterior - $mov->saldo_resultante;
                }
                else {
                    $total_sangria              += $mov->saldo_resultante = $mov->saldo_anterior;
                    $total_sangrias_suprimentos += $mov->saldo_resultante - $mov->saldo_anterior;
                }
            }
    
            $data->total_recebimentos         = $total_recebimentos;
            $data->total_vendas               = $total_vendas;
            $data->total_sangrias_suprimentos = $sangria + $suprimento;
            $data->total_suprimento           = $suprimento;
            $data->total_sangria              = $sangria;
    
            $data->data_selecionada = $data_externa;
    
            $data->usuario = Auth::user()->name;
    
            $data->message = getCaixa()->situacao;
            
            if (getCaixa()->situacao == 'A' && $T_inicial->data == date('Y-m-d')) {
                $data->msg1 = "O Caixa de hoje ainda está aberto";
            } 
            else if (getCaixa()->situacao == 'A' && $T_inicial->data < date('Y-m-d')) {
                $data->msg1 = "Feche o caixa de ". date("d/m/Y", strtotime($T_inicial->data));
            }
            else $data->msg1 = "O Caixa de hoje ainda está fechado";
        }
        else{
            $data = new \StdClass;

            $data->id_caixa           = getCaixa()->id;
            $data->saldo_caixa        = getCaixa()->valor;
            $data->saldo_inicial      = 0;
            $data->suprimento         = 0; 
            $data->recebimento_vista  = 0; 
            $data->recebimento_prazo  = 0; 
            $data->sangria            = 0; 
            $data->total_cartao         = 0;
            $data->total_cartao_debito  = 0;
            $data->total_cartao_credito = 0;
            $data->total_transferencia = 0;
            $data->valor_pix           = 0;
            $data->valor_transferencia = 0;
            $data->total_boleto    = 0;
            $data->total_duplicata = 0;
            $data->total_convenio = 0;
            $data->lista_convenios = array();
            $data->total_recebimentos         = 0;
            $data->total_vendas               = 0;
            $data->total_sangrias_suprimentos = 0;
            $data->total_suprimento           = 0;
            $data->total_sangria              = 0;
            $data->data_selecionada = $data_externa;
            $data->usuario = Auth::user()->name;
            $data->message = getCaixa()->situacao;
            $data->msg1 = "Não foi encontrato registros desse caixa";
        }

                            // }
            
        

        return json_encode($data);

    }


    public function abrir(Request $request){
        $caixa = Caixa::find($request->id);
        $caixa->situacao = 'A';
        $caixa->d_ult_abertura = date('Y-m-d');
        $caixa->h_ult_abertura = date('H:i:s');
        $caixa->save();

        $caixa_mov = new CaixaMov;
        $caixa_mov->id_caixa         = $caixa->id;
        $caixa_mov->descr            = 'Abertura de caixa';
        $caixa_mov->id_forma_pag     = 0;
        $caixa_mov->valor            = $caixa->valor;
        $caixa_mov->tipo             = 'A';
        $caixa_mov->data             = date('Y-m-d');
        $caixa_mov->hora             = date('H:i:s');
        $caixa_mov->saldo_anterior   = $caixa->valor;
        $caixa_mov->saldo_resultante = $caixa->valor;
        $caixa_mov->created_by       = Auth::user()->id;
        $caixa_mov->created_by_descr = Auth::user()->name;
        $caixa_mov->save();

        return 'S';
    }

    public function fechar_caixa(Request $request){
        $caixa = Caixa::find($request->id_caixa);
        $caixa->situacao = 'F';
        $caixa->d_ult_fechamento = date('Y-m-d');
        $caixa->h_ult_fechamento = date('H:i:s');
        $caixa->save();

        $caixa_mov = new CaixaMov;
        $caixa_mov->id_caixa = $caixa->id;
        $caixa_mov->descr            = 'Fechamento de caixa';
        $caixa_mov->id_forma_pag     = 0;
        $caixa_mov->saldo_anterior   = $caixa->valor;
        $caixa_mov->saldo_resultante = $caixa->valor;
        $caixa_mov->valor            = $caixa->valor;
        $caixa_mov->tipo             = 'F';
        $caixa_mov->data             = date('Y-m-d');
        $caixa_mov->hora             = date('H:i:s');
        $caixa_mov->created_by       = Auth::user()->id;
        $caixa_mov->created_by_descr = Auth::user()->name;
        $caixa_mov->save();

        return 'true';
    }

    public function abrir_modal_saldo(Request $request){
        $caixa = Caixa::find($request->id_caixa);
        if ($caixa->valor > 0) return $caixa->valor;
        else                   return 0.00;
    }



    public function salvar_valor_caixa(Request $request) {
        $caixa = Caixa::find($request->id_caixa);

        $caixa_mov = new CaixaMov;
        $caixa_mov->id_caixa = $request->id_caixa;
        $caixa_mov->descr = $request->obs;
        $caixa_mov->id_forma_pag = 0;
        $caixa_mov->valor = $caixa->valor;
        $caixa_mov->tipo = 'R';
        $caixa_mov->data = date('Y-m-d');
        $caixa_mov->hora = date('H:i:s');
        $caixa_mov->saldo_anterior = $caixa->valor;
        $caixa_mov->saldo_resultante = $request->valor;
        $caixa_mov->created_by = Auth::user()->id_profissional;
        $caixa_mov->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
        $caixa_mov->save();

        $caixa->valor = $request->valor;
        $caixa->save();

        return 'true';
    }

    // AQUI
    public function resumo_fechamento(Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $data_externa)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $id)
                    ->where('caixa_mov.data', $data_externa)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>',  $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
                    
        $data = new \StdClass;
        $data->datainicial = $T_inicial->data;
        $data->datafinal   = $T_final->data;

        
        $historico = DB::table('caixa_mov')
                     ->select('caixa_mov.id AS cod_operacao',
                              'caixa_mov.descr AS descr_operacao',
                              DB::raw(
                                "CASE WHEN (caixa_mov.id_forma_pag <> 0) THEN 'Não Informado'
                                ELSE forma_pag.descr END AS descr_forma_pag"
                              ),
                              'caixa_mov.saldo_anterior   AS saldo_anterior',
                              'caixa_mov.saldo_resultante AS saldo_resultante',
                              'caixa_mov.hora             AS hora'
                     )
                     ->leftjoin('forma_pag', 'forma_pag.id', 'caixa_mov.id_forma_pag')
                     ->where('caixa_mov.id_caixa', $request->id_caixa)
                     ->where('caixa_mov.id', '>=', $abertura->id)
                     ->orderBy('caixa_mov.id')
                     ->get();
    }


    public function teste() {
        $pedidos = DB::table('pedido')
                   ->where('lixeira', 0)
                   ->get();

        $atualizados = 0;
        foreach($pedidos AS $pedido) {
            $aux_conversao = DB::table('mov_credito')
                              ->where('id_pedido', $pedido->id)
                              ->where('mov_credito.tipo_transacao', 'E')
                              ->count();
            
            if ($aux_conversao == 0) {
                $atualizados++;
                $pedido_planos = DB::table('pedido_planos')
                                 ->where('pedido_planos.id_pedido', $pedido->id)
                                 ->get();
                foreach($pedido_planos As $pedido_plano){
                    $p = PedidoPlanos::find($pedido_plano->id);
                    $p->qtd_total = TabelaPrecos::find($pedido_plano->id_plano)->max_atv;
                    $p->save();
                }
            }
        }
        return "Atualizados: " . $atualizados;
    }

    public function teste31232333() {
        $pedidos = DB::select(
            DB::raw("
            select * from
                (
                SELECT
                    COUNT(pedido.id) as qtd,
                    MIN(pedido.id) AS id,
                    pedido.id_paciente, 
                    pedido.data, 
                    pedido.ids_planos 
                FROM 
                    pedido
                WHERE
                    pedido.lixeira = 0 and 
                    pedido.ids_planos is not null
                GROUP BY 
                    pedido.id_paciente, 
                    pedido.data, 
                    pedido.ids_planos
                ORDER BY 
                    COUNT(pedido.id) DESC
                ) As tabAux 
            where qtd > 1
            ")
        );
        $pedidos_excluidos = 0;
        foreach($pedidos AS $pedido) {
            $agendamentos = DB::select(
                DB::raw("select * from agenda where agenda.id_pedido = " + $pedido->id)
            );
            if (sizeof($agendamentos) > 0) {
                foreach($agendamentos AS $agendamento) {
                    $data = Agenda::find($agendamento->id);
                    $data->id_pedido = 9999;
                    $data->save();
                }
            }
            $data = Pedido::find($pedido->id);
            $data->lixeira = true;

            $data->save();

            $pedidos_excluidos++;
        }
        return "Contratos excluídos: " . $pedidos_excluidos;
    }



    

    public function teste10() {
        $lista1 = array('28480001174', '28480001255', '27414', '28480001156', '10875', '18335', '28480001050', '28480001051', '13115', '27633', '18198', '18199', '10861', '12996', '1126', '210', '28480001372', '28480001376', '1227', '1259', '822', '765', '6508', '14826', '28480001248', '28480001257', '27586', '2790', '1386', '1387', '18276', '18278', '28480001339', '28480001185', '18327', '28569', '28480001160', '28480001268', '15947', '17005', '28480001196', '28480001197', '1186', '1732', '4372', '4373', '1520', '1522', '28480000104', '28480000109');
        $lista2 = array('28480001255', '28480001156', '18335', '28480001051', '27633', '18199', '12996', '1126', '28480001376', '1259', '822', '14826', '28480001257', '27586', '1387', '18278', '28480001339', '28569', '28480001268', '17005', '28480001197', '1732', '4373', '1522', '28480000109');
        // $lista2 = array('28480001280', '28480001351', '28480001106', '2455', '28125', '2672', '8751', '28480001483', '6480', '6570', '8745', '18136', '3105', '28419', '3182', '28480001193', '28480001383', '2116', '22121', '2311', '28308', '1301', '21755', '28480001232', '28480001316', '12952');


        foreach($lista1 AS $item) {
            if (in_array($item, $lista2)) {
                $pessoa = Pessoa::find($item);
                $pessoa->lixeira = 0;
                $pessoa->save();
            }
            else {
                $pessoa = Pessoa::find($item);
                $pessoa->lixeira = 1;
                $pessoa->save();
            }
        }
        return $pessoa;
        
    }

















    public function teste2() {
        // $contratos = DB::table('pedido')
        //              ->where('lixeira', 0)
        //              ->get();

        // foreach($contratos AS $contrato) {
        //     $aux = Pedido::find($contrato->id);

        //     
        //     $aux->data_validade = 0;
        // }
        $pedido_planos = DB::table('pedido_planos')
                        ->get();

        foreach($pedido_planos As $plano) {
            $aux = DB::table('tabela_precos_vigencia')
                       ->where('id_tabela_preco', $plano->id_plano)
                       ->where('de', '<=', $plano->qtde)
                       ->where('ate', '>=', $plano->qtde)
                       ->first();
                
            if ($aux) {
                if      ($aux->vigencia == 30) $data_validade = date('Y-m-d', strtotime('+1 month'));
                else if ($aux->vigencia == 60) $data_validade = date('Y-m-d', strtotime('+2 month'));
                else if ($aux->vigencia == 90) $data_validade = date('Y-m-d', strtotime('+3 month'));
                else if ($aux->vigencia == 180)$data_validade = date('Y-m-d', strtotime('+6 month'));
                else if ($aux->vigencia == 360)$data_validade = date('Y-m-d', strtotime('+1 year'));
                else                           $data_validade = date('Y-m-d');
            }
            else {
                $vigencia = TabelaPrecos::find($plano->id_plano);

                if      ($vigencia->vigencia == 30)  $data_validade = date('Y-m-d', strtotime('+1 month'));
                else if ($vigencia->vigencia == 60)  $data_validade = date('Y-m-d', strtotime('+2 month'));
                else if ($vigencia->vigencia == 90)  $data_validade = date('Y-m-d', strtotime('+3 month'));
                else if ($vigencia->vigencia == 180) $data_validade = date('Y-m-d', strtotime('+6 month'));
                else if ($vigencia->vigencia == 360) $data_validade = date('Y-m-d', strtotime('+1 year'));
                else                                 $data_validade = date('Y-m-d');
            }

            $save = PedidoPlanos::find($plano->id);
            $save->data_validade = $data_validade;
            $save->save();
        }

        $pedidos = DB::table('pedido')
                    ->where('lixeira', 0)
                    ->get();

        foreach($pedidos AS $pedido) {
            $maior = '2022-10-11';
            $planos_aux = DB::table('pedido_planos')
                          ->where('pedido_planos.id_pedido', $pedido->id)
                          ->get();
            foreach($planos_aux AS $aux) {
                if (strtotime($aux->data_validade) > strtotime($maior)) {
                    $maior = $aux->data_validade;
                }
            }

            $save = Pedido::find($pedido->id);
            $save->data_validade = $maior;
            $save->save();
                          
        }
    }






    public function extrato_dinheiro(Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        // return json_encode($T_inicial->data);
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Troco',
                              'Saldo Antes',
                              'Saldo Depois',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                DB::raw("CASE WHEN (caixa_mov.tipo <> 'R') THEN caixa_mov.valor
                                              WHEN (caixa_mov.saldo_anterior > caixa_mov.saldo_resultante) THEN (caixa_mov.saldo_anterior - caixa_mov.saldo_resultante)
                                        ELSE (caixa_mov.saldo_resultante - caixa_mov.saldo_anterior) END AS valor_total"),
                                DB::raw('SUM(pedido_forma_pag.troco) AS troco'),
                                'caixa_mov.saldo_anterior',
                                'caixa_mov.saldo_resultante',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo',
                                'caixa_mov.id_forma_pag')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        // ->whereRaw("((caixa_mov.id_forma_pag = 2 or caixa_mov.tipo = 'R') AND (pedido.lixeira = 0 or pedido.id is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_forma_pag', 2)
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->groupBy('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'caixa_mov.valor',
                                'caixa_mov.saldo_anterior',
                                'caixa_mov.saldo_resultante',
                                'caixa_mov.data',
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo','caixa_mov.id_forma_pag')
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
        return json_encode($data);
    }
    
    public function extrato_cartao(Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();

        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                 'pessoa.nome_fantasia',
                                 'pedido_forma_pag.valor_total',
                                 DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                 'caixa_mov.hora',
                                 'created_by_descr',
                                 'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', function($join) {
                            $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                            $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                        })
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(pedido_forma_pag.id_forma_pag in (1, 3, 6) AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
        return json_encode($data);
    }
    
    public function extrato_transferencia (Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(pedido_forma_pag.id_forma_pag in (4,5) and (pedido.lixeira = 0 or pedido.lixeira is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                        
        return json_encode($data);
    }
    
    public function extrato_convenio (Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();
                    $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        $data = new \StdClass;
        // return json_encode($T_inicial);
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(pedido.id_convenio <> 0 and (pedido.lixeira = 0 or pedido.lixeira is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                        
        return json_encode($data);
    }
    
    public function extrato_recebimentos (Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        
            
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'caixa_mov.valor AS valor_total',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'caixa_mov.created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->where('caixa_mov.tipo', 'E')
                        ->where(function($sql) {
                            $sql->where('pedido.lixeira', 0)
                                ->orWhere('pedido.lixeira', 'is',null);
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->groupBy('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'caixa_mov.valor',
                                'caixa_mov.data',
                                'caixa_mov.hora',
                                'caixa_mov.created_by_descr',
                                'caixa_mov.tipo')
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                        
        return json_encode($data);
    }

    public function listar_caixas($id) {
        return json_encode(
            DB::table('caixa_operadores')
            ->select('pessoa.nome_fantasia As nome')
            ->join('pessoa', 'pessoa.id', 'caixa_operadores.id_operador')
            ->where('caixa_operadores.id_caixa', $id)
            ->get()
        );
    }
    
    public function extrato_vendas (Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'caixa_mov.created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->leftjoin('titulos_receber', 'titulos_receber.id_pedido', 'pedido.id')
                        ->where(function($sql) {
                            $sql->where('pedido.lixeira', 0)
                                ->orWhere('pedido.lixeira', 'is',null);
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('pedido_forma_pag.valor_total', '>', 0)
                        ->groupBy('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                'caixa_mov.data',
                                'caixa_mov.hora',
                                'caixa_mov.created_by_descr',
                                'caixa_mov.tipo')
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                        
        return json_encode($data);
    }
    
    public function extrato_sangria_suprimento (Request $request) {
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Troco',
                              'Saldo Antes',
                              'Saldo Depois',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                'pedido_forma_pag.troco',
                                'caixa_mov.saldo_anterior',
                                'caixa_mov.saldo_resultante',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(caixa_mov.tipo = 'E' and (pedido.lixeira = 0 and pedido.lixeira is null))")
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();

        return json_encode($data);
    }
    
    public function extrato_final(Request $request){    
        $T_inicial = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'A')
                    ->orderBy('caixa_mov.data')
                    ->orderBy('caixa_mov.hora')
                    ->first();

        $T_final = DB::table('caixa_mov')
                    ->select('caixa_mov.data', 'caixa_mov.hora')
                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                    ->where('caixa_mov.data', $request->data_selecionada)
                    ->where('tipo', 'F')
                    ->where('caixa_mov.data', '>=', $T_inicial->data)
                    ->where('caixa_mov.hora', '>', $T_inicial->hora)
                    ->orderBy('caixa_mov.data', 'DESC')
                    ->orderBy('caixa_mov.hora', 'DESC')
                    ->first();

        $array = array();
        if ($T_inicial) {

            // DINHEIRO \\
            $data = new \StdClass;
            $data->header = array('Tipo',
                                'Descr.',
                                'Pessoa',
                                'Valor Total',
                                'Troco',
                                'Saldo Antes',
                                'Saldo Depois',
                                'Data',
                                'Hora',
                                'Operador');
            
            $data->header_medidas = array(5, 15, 15, 6, 6, 6, 6, 5, 5, 12);
            $data->header_align = array('text-left',
                                        'text-left',
                                        'text-left',
                                        'text-right',
                                        'text-right',
                                        'text-right',
                                        'text-right',
                                        'text-right',
                                        'text-right',
                                        'text-left');
            $data->mov = DB::table('caixa_mov')
                                    ->select('caixa_mov.descr',
                                            'pessoa.nome_fantasia',
                                            DB::raw("CASE WHEN (caixa_mov.tipo <> 'R') THEN caixa_mov.valor
                                                          WHEN (caixa_mov.saldo_anterior > caixa_mov.saldo_resultante) THEN (caixa_mov.saldo_anterior - caixa_mov.saldo_resultante)
                                                    ELSE (caixa_mov.saldo_resultante - caixa_mov.saldo_anterior) END AS valor_total"),
                                            DB::raw('SUM(pedido_forma_pag.troco) AS troco'),
                                            'caixa_mov.saldo_anterior',
                                            'caixa_mov.saldo_resultante',
                                            DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                            'caixa_mov.hora',
                                            'created_by_descr',
                                            'caixa_mov.tipo',
                                            'caixa_mov.id_forma_pag')
                                    ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                                    ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'pedido.id')
                                    ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                                    // ->whereRaw("((caixa_mov.id_forma_pag = 2 or caixa_mov.tipo = 'R') AND (pedido.lixeira = 0 or pedido.id is null))")
                                    ->where(function($sql) use($T_inicial, $T_final){
                                        if ($T_inicial){
                                            if($T_final){
                                                $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                                    ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                                    ->where('caixa_mov.data',"<=", $T_final->data)
                                                    ->where('caixa_mov.hora',"<=", $T_final->hora);
                                            }
                                            else {
                                                $sql->where('caixa_mov.data', $T_inicial->data)
                                                    ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                            }
                                            
                                        }
                                    })
                                    ->where('caixa_mov.id_forma_pag', 2)
                                    ->where('caixa_mov.id_caixa', $request->id_caixa)
                                    ->groupBy('caixa_mov.descr',
                                            'pessoa.nome_fantasia',
                                            'caixa_mov.valor',
                                            'caixa_mov.saldo_anterior',
                                            'caixa_mov.saldo_resultante',
                                            'caixa_mov.data',
                                            'caixa_mov.hora',
                                            'created_by_descr',
                                            'caixa_mov.tipo','caixa_mov.id_forma_pag')
                                    ->orderBy('caixa_mov.data')
                                    ->orderBy('caixa_mov.hora')
                                    ->get();
            array_push($array, $data);







            // CARTAO \\
        $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                 'pessoa.nome_fantasia',
                                 'pedido_forma_pag.valor_total',
                                 DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                 'caixa_mov.hora',
                                 'created_by_descr',
                                 'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', function($join) {
                            $join->on('pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido');
                            $join->on('pedido_forma_pag.id_forma_pag', 'caixa_mov.id_forma_pag');
                        })
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(pedido_forma_pag.id_forma_pag in (1, 3) AND (pedido.lixeira = 0 or pedido.lixeira is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                array_push($array, $data);





        
            // TRANFERENCIAS \\
            $data = new \StdClass;
            $data->header = array('Tipo',
                                'Descr.',
                                'Pessoa',
                                'Valor Total',
                                'Data',
                                'Hora',
                                'Operador');
            
            $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
            $data->header_align = array('text-left',
                                        'text-left',
                                        'text-left',
                                        'text-right',
                                        'text-right',
                                        'text-right',
                                        'text-left');
            $data->mov =  DB::table('caixa_mov')
                            ->select('caixa_mov.descr',
                                    'pessoa.nome_fantasia',
                                    'pedido_forma_pag.valor_total',
                                    'caixa_mov.data',
                                    'caixa_mov.hora',
                                    'created_by_descr',
                                    'caixa_mov.tipo')
                            ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                            ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                            ->whereRaw("(pedido_forma_pag.id_forma_pag in (4,5) and (pedido.lixeira = 0 or pedido.lixeira is null))")
                            ->where(function($sql) use($T_inicial, $T_final){
                                if ($T_inicial){
                                    if($T_final){
                                        $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                            ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                    }
                                    else {
                                        $sql->where('caixa_mov.data', $T_inicial->data)
                                            ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                    }
                                    
                                }
                            })
                            ->orderBy('caixa_mov.data')
                            ->orderBy('caixa_mov.hora')
                            ->get();

            // array_push($array, $data);



            // CONVENIO \\
            $data = new \StdClass;
        $data->header = array('Tipo',
                              'Descr.',
                              'Pessoa',
                              'Valor Total',
                              'Data',
                              'Hora',
                              'Operador');
        
        $data->header_medidas = array(5, 15, 15, 6, 6, 5, 5, 12);
        $data->header_align = array('text-left',
                                    'text-left',
                                    'text-left',
                                    'text-right',
                                    'text-right',
                                    'text-right',
                                    'text-left');
        $data->mov =  DB::table('caixa_mov')
                        ->select('caixa_mov.descr',
                                'pessoa.nome_fantasia',
                                'pedido_forma_pag.valor_total',
                                DB::raw("DATE_FORMAT(caixa_mov.data, '%d/%m/%Y') AS data"),
                                'caixa_mov.hora',
                                'created_by_descr',
                                'caixa_mov.tipo')
                        ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
                        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
                        ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
                        ->whereRaw("(pedido.id_convenio <> 0 and (pedido.lixeira = 0 or pedido.lixeira is null))")
                        ->where(function($sql) use($T_inicial, $T_final){
                            if ($T_inicial){
                                if($T_final){
                                    $sql->where('caixa_mov.data',">=", $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora)
                                        ->where('caixa_mov.data',"<=", $T_final->data)
                                        ->where('caixa_mov.hora',"<=", $T_final->hora);
                                }
                                else {
                                    $sql->where('caixa_mov.data', $T_inicial->data)
                                        ->where('caixa_mov.hora',">=", $T_inicial->hora);
                                }
                                
                            }
                        })
                        ->where('caixa_mov.id_caixa', $request->id_caixa)
                        ->orderBy('caixa_mov.data')
                        ->orderBy('caixa_mov.hora')
                        ->get();
                array_push($array, $data);
        }




        
        

        $array_valores = array();

        if ($T_inicial) {
            array_push($array_valores, getCaixa()->valor);
           array_push($array_valores, DB::table('caixa_mov')
           ->select(DB::raw('CASE WHEN (SUM(caixa_mov.valor) is not null) THEN SUM(pedido_forma_pag.valor_total)
                             ELSE 0 END AS total'))
           ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
           ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
           ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
           ->whereRaw("(pedido_forma_pag.id_forma_pag in (1, 3) AND (pedido.lixeira = 0 or pedido.lixeira is null))")
           ->where(function($sql) use($T_inicial, $T_final){
               if ($T_inicial){
                   if($T_final){
                       $sql->where('caixa_mov.data',">=", $T_inicial->data)
                           ->where('caixa_mov.hora',">=", $T_inicial->hora)
                           ->where('caixa_mov.data',"<=", $T_final->data)
                           ->where('caixa_mov.hora',"<=", $T_final->hora);
                   }
                   else {
                       $sql->where('caixa_mov.data', $T_inicial->data)
                           ->where('caixa_mov.hora',">=", $T_inicial->hora);
                   }
                   
               }
           })
           ->where('caixa_mov.id_caixa', $request->id_caixa)
           ->orderBy('caixa_mov.data')
           ->orderBy('caixa_mov.hora')
           ->value('total'));


           array_push($array_valores, DB::table('caixa_mov')
           ->select(DB::raw('CASE WHEN (SUM(caixa_mov.valor) is not null) THEN SUM(pedido_forma_pag.valor_total)
                             ELSE 0 END AS total'))
           ->leftjoin('pedido', 'pedido.id', 'caixa_mov.id_pedido')
           ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id_pedido', 'caixa_mov.id_pedido')
           ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_paciente')
           ->whereRaw("(pedido.id_convenio <> 0 and (pedido.lixeira = 0 or pedido.lixeira is null))")
           ->where(function($sql) use($T_inicial, $T_final){
               if ($T_inicial){
                   if($T_final){
                       $sql->where('caixa_mov.data',">=", $T_inicial->data)
                           ->where('caixa_mov.hora',">=", $T_inicial->hora)
                           ->where('caixa_mov.data',"<=", $T_final->data)
                           ->where('caixa_mov.hora',"<=", $T_final->hora);
                   }
                   else {
                       $sql->where('caixa_mov.data', $T_inicial->data)
                           ->where('caixa_mov.hora',">=", $T_inicial->hora);
                   }
                   
               }
           })
           ->where('caixa_mov.id_caixa', $request->id_caixa)
           ->orderBy('caixa_mov.data')
           ->orderBy('caixa_mov.hora')
           ->value('total'));
        }

        $data = new \StdClass;
        $data->array = $array;
        $data->array_valores = $array_valores;
        
        return json_encode($data);
    }







    public function teste3(){
        $pedidos = DB::table('pedido')
                   ->where('pedido.data', '>=', '2022-11-22')
                   ->where('lixeira', 0)
                   ->whereRaw('(id_caixa is not null)')
                   ->get();
        
        // return $pedidos;
        foreach($pedidos As $pedido) {
            $aux_caixa = DB::table('caixa_mov')
                         ->where('id_pedido', $pedido->id)
                         ->where('descr', 'Venda de Contrato')
                         ->count();

            $aux_titulos = DB::table('titulos_receber')
                         ->where('id_pedido', $pedido->id)
                         ->where('descr', 'Venda de Contrato')
                         ->count();
            $pedido_forma_pag = DB::table('pedido_forma_pag')
                                ->where('id_pedido', $pedido->id)
                                ->get();
            
            foreach($pedido_forma_pag AS $forma_pag) {
                if ($aux_caixa == 0) {
                    $caixa = Caixa::find($pedido->id_caixa);
    
                    $caixa_mov = new CaixaMov;
                    $caixa_mov->id_caixa = $pedido->id_caixa;
                    $caixa_mov->id_pedido = $pedido->id;
                    $caixa_mov->descr = "Venda de Contrato";
                    if ($forma_pag->id_forma_pag != '') $caixa_mov->id_forma_pag = $forma_pag->id_forma_pag;
                    $caixa_mov->valor = $forma_pag->valor_total;
                    $caixa_mov->tipo  = "E";
                    $caixa_mov->data = date('Y-m-d');
                    $caixa_mov->hora = date('H:i:s');
                    $caixa_mov->created_by = $pedido->id_prof_exa;
                    $caixa_mov->created_by_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;
                    $caixa_mov->saldo_anterior = $caixa->valor;
    
                    if ($forma_pag->id_forma_pag == 2) {
                        $caixa_mov->saldo_resultante = ($caixa->valor - $forma_pag->troco + $forma_pag->valor_total);
                    }
                    else $caixa_mov->saldo_resultante = $caixa->valor;
                    $caixa_mov->save();
    
                    $caixa->valor = $caixa_mov->saldo_resultante;
                    $caixa->save();
                }
                if ($aux_titulos = 0) {
                    $parcelas = DB::table('pedido_parcela')
                                ->where('id_pedido_forma_pag', $forma_pag->id)
                                ->get();
                    
                    foreach($parcelas AS $parcela) {
                        
                        // // FINANCEIRO \\
                        $tituloreceber = new TitulosReceber;
                        if ($pedido->id_caixa) $tituloreceber->id_caixa = $pedido->id_caixa;
                        else            $tituloreceber->id_caixa = 0;
                        $tituloreceber->id_financeira = $forma_pag->id_financeira;
                        $tituloreceber->descr = 'Venda de contrato';
                        $tituloreceber->origem = 'Pedido';
                        $tituloreceber->parcela = $parcela->parcela;
                        $tituloreceber->id_forma_pag = $forma_pag->id_forma_pag;
                        $tituloreceber->forma_pag = FormaPag::find($forma_pag->id_forma_pag)->descr;
                        $tituloreceber->id_pedido = $pedido->id;
                        $tituloreceber->id_pedido_forma_pag = $forma_pag->id;
                        $tituloreceber->ndoc = $pedido->id;
                        $tituloreceber->id_pessoa = $pedido->id_paciente;
                        $tituloreceber->d_entrada = $pedido->data;
                        $tituloreceber->h_entrada = $pedido->hora;
                        $tituloreceber->d_emissao = $pedido->data;
                        $tituloreceber->d_vencimento = $parcela->vencimento;

                        // DINHEIRO, TRANSFERENCIA E PIX \\
                        if ($forma_pag->id_forma_pag == 2 || $forma_pag->id_forma_pag == 4 || $forma_pag->id_forma_pag == 5){
                            $tituloreceber->d_pago = $pedido->data;
                            $tituloreceber->h_pago = $pedido->hora;
                            $tituloreceber->pago = 'S';
                            $tituloreceber->pago_por = $pedido->id_prof_exa;
                            $tituloreceber->pago_por_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;
                            $tituloreceber->valor_total = $parcela->valor;
                            $tituloreceber->valor_total_pago = $parcela->valor;
                        }

                        // CARTAO \\
                        else if ($forma_pag->id_forma_pag == 1 || $forma_pag->id_forma_pag == 3){
                            // ****************************************** \\
                            // Dando Baixa no título em nome do associado \\
                            $tituloreceber->d_pago = $pedido->data;
                            $tituloreceber->h_pago = $pedido->hora;
                            $tituloreceber->pago = 'S';
                            $tituloreceber->pago_por = $pedido->id_prof_exa;
                            $tituloreceber->pago_por_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;
                            $tituloreceber->valor_total = $parcela->valor;
                            $tituloreceber->valor_total_pago = $parcela->valor;
                            $tituloreceber->movimento = 'N';
                            $tituloreceber->created_by = $pedido->id_prof_exa;
                            $tituloreceber->created_by_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;
                            $tituloreceber->updated_by = $pedido->id_prof_exa;
                            $tituloreceber->updated_by_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;

                            $tituloreceber->obs = 'Compra de contrato';
                            $tituloreceber->save();


                            // ************************************ \\
                            // Criando Título em nome da financeira \\
                            $tituloreceber = new TitulosReceber;
                            $tituloreceber->id_caixa            = 0;
                            $tituloreceber->id_financeira       = $forma_pag->id_financeira;
                            $tituloreceber->descr               = 'Venda de contrato';
                            $tituloreceber->origem              = 'Pedido';
                            $tituloreceber->parcela             = ($i + 1);
                            $tituloreceber->id_forma_pag        = $forma_pag->id_forma_pag;
                            $tituloreceber->forma_pag           = FormaPag::find($forma_pag->id_forma_pag)->descr;
                            $tituloreceber->id_pedido           = $pedido->id;
                            $tituloreceber->forma_pag = $forma_pag->id;
                            $tituloreceber->ndoc                = $pedido->id;
                            $tituloreceber->id_pessoa           = $pedido->id_paciente;
                            $tituloreceber->d_entrada = $pedido->data;
                            $tituloreceber->h_entrada = $pedido->hora;
                            $tituloreceber->d_emissao = $pedido->data;
                            $tituloreceber->d_vencimento = $parcela->vencimento;

                            $tituloreceber->valor_total = $parcela->valor;
                            $tituloreceber->taxa_financeira = 0;
                        }
                        else {
                            $tituloreceber->valor_total = $forma_pag->valor_total;
                        }
                        $tituloreceber->created_by = $pedido->id_prof_exa;
                        $tituloreceber->created_by_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;
                        $tituloreceber->updated_by = $pedido->id_prof_exa;
                        $tituloreceber->updated_by_descr = Pessoa::find($pedido->id_prof_exa)->nome_fantasia;

                        $tituloreceber->obs = 'Compra de contrato';
                        $tituloreceber->save();
                    }
                }
            }
        }
    }




    public function teste4() {
        $tabela_precos = DB::table('tabela_precos')
                         ->get();

        DB::table('empresas_plano')->delete();

        foreach($tabela_precos AS $tabela) {
            if ($tabela->id_emp == 1) {
                $empresa_plano = new EmpresasPlano;
                $empresa_plano->id_tabela_preco = $tabela->id;
                $empresa_plano->id_emp = 1;
                $empresa_plano->save();

                $empresa_plano = new EmpresasPlano;
                $empresa_plano->id_tabela_preco = $tabela->id;
                $empresa_plano->id_emp = 2;
                $empresa_plano->save();
            }
            else {
                $empresa_plano = new EmpresasPlano;
                $empresa_plano->id_tabela_preco = $tabela->id;
                $empresa_plano->id_emp          = $tabela->id;
                $empresa_plano->save();
            }
        }
    }
}
