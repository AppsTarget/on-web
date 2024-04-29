<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use App\Pedido;
use App\PedidoFormaPag;
use App\PedidoParcela;
use App\PedidoPlanos;
use App\Agenda;
use App\MovCredito;
use App\OldModalidades;
use App\OldAtividades;
use App\Pessoa;
use App\FormaPag;
use App\TitulosReceber;
use App\TabelaPrecos;
use App\Caixa;
use App\Financeira;
use App\CaixaMov;
use App\MovConta;
use App\Desconto;
use App\Encaminhamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gerar_num() {
        $num_pedido = DB::table('pedido')->max('id');
        if ($num_pedido == '') $num_pedido = 1;
        else                   $num_pedido++;

        return ["num_pedido" => $num_pedido];
    }

    public function mostrar($id_pedido) {
        try {
            $data = new \stdClass;
            $data->pedido = DB::table('pedido')
                            ->select(
                                'pedido.*',
                                'paciente.nome_fantasia AS descr_paciente',
                                'prof_examinador.nome_fantasia AS descr_prof_exa',
                                'convenio.descr AS descr_convenio'
                            )
                            ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
                            ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'pedido.id_prof_exa')
                            ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                            ->where('pedido.id', $id_pedido)
                            ->where(function($sql){
                                $sql->where('lixeira', false)
                                ->orWhere('lixeira', null);
                            })
                            ->first();

            $data->convenio_paciente = DB::table('convenio_pessoa')
                        ->select(
                            'convenio.id',
                            'convenio.descr'
                        )
                        ->leftjoin('convenio', 'convenio.id', 'convenio_pessoa.id_convenio')
                        ->where('convenio.id_emp', getEmpresa())
                        ->where(function($sql) use($data) {
                            $sql->where('convenio_pessoa.id_paciente', $data->pedido->id_paciente)
                                ->orWhere('convenio.quem_paga', 'E');
                        })
                        ->groupby(
                            'convenio.id',
                            'convenio.descr'
                        )
                        ->get();

            $data->ped_procedimentos = DB::table('pedido_servicos')
                            ->select(
                                'pedido_servicos.*',
                                DB::raw(
                                    "CASE" .
                                    "   WHEN pessoa.nome_reduzido IS NOT NULL AND pessoa.nome_reduzido <> '' THEN pessoa.nome_reduzido" .
                                    "   ELSE pessoa.nome_fantasia " .
                                    "END AS descr_prof_exe"
                                ),
                                'procedimento.descr AS descr_procedimento',
                                'preco.valor AS valor_vista',
                                'preco.valor_prazo'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido_servicos.id_prof_exe')
                            ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
                            ->leftjoin('preco', function($join) {
                                $join->on('preco.id_tabela_preco', 'pedido_servicos.id_tabela_preco');
                                $join->on('preco.id_procedimento', 'pedido_servicos.id_procedimento');
                            })
                            ->where('pedido_servicos.id_pedido', $id_pedido)
                            ->get();

            $data->ped_formas_pag = DB::table('pedido_forma_pag')
                                ->select(
                                    'pedido_forma_pag.*',
                                    'forma_pag.descr AS descr_forma_pag',
                                    'financeira.descr AS descr_financeira',
                                    DB::raw(
                                        '(SELECT pedido_parcela.vencimento' .
                                        '   FROM pedido_parcela' .
                                        '  WHERE pedido_parcela.id_pedido_forma_pag = pedido_forma_pag.id' .
                                        '    AND pedido_parcela.parcela = 1' .
                                        '  LIMIT 1) AS data_vencimento'
                                    )
                                )
                                ->leftjoin('forma_pag',  'forma_pag.id',  'pedido_forma_pag.id_forma_pag')
                                ->leftjoin('financeira', 'financeira.id', 'pedido_forma_pag.id_financeira')
                                ->where('pedido_forma_pag.id_pedido', $id_pedido)
                                ->get();

            foreach ($data->ped_formas_pag as $forma_pag) {
                $data->ped_formas_pag->parcela = DB::table('pedido_parcela')
                        ->where('id_pedido_forma_pag', $forma_pag->id)
                        ->get();
            }

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        $query = "
            SELECT old_contratos.id, old_contratos.id                                AS num_pedido, 
                    CASE WHEN (old_contratos.situacao = 1) THEN 'F'
                    ELSE old_contratos.situacao END                                   AS status,
                    old_contratos.pessoas_id                                          AS id_paciente,
                    pessoa.nome_fantasia                                              AS descr_paciente,
                    GROUP_CONCAT(TRIM(old_financeira.descr) SEPARATOR '@')            AS descr_convenio,
                    old_contratos.responsavel                                         AS descr_prof_exa,
                    old_contratos.responsavel                                         AS created_by,
                    CONCAT(old_contratos.datainicial, ' ', old_contratos.horainicial) AS created_at,
                    CONCAT(old_contratos.datafinal, ' ', old_contratos.horafinal)     AS data_validade,
                    old_contratos.valor_contrato                                      AS total,
                    1 AS sistema_antigo
            FROM `old_contratos`
                INNER JOIN pessoa on old_contratos.pessoas_id = pessoa.id
                INNER JOIN old_finanreceber on old_contratos.id = old_finanreceber.id_contrato
                INNER JOIN old_financeira on old_finanreceber.id_financeira = old_financeira.id
            WHERE
                old_contratos.id_emp = ". getEmpresa() ."
            GROUP BY old_contratos.id, old_contratos.situacao, old_contratos.situacao, old_contratos.pessoas_id, pessoa.nome_fantasia, old_contratos.responsavel, old_contratos.datainicial, old_contratos.horainicial, old_contratos.datafinal, old_contratos.horafinal, old_contratos.valor_contrato

            UNION ALL

            SELECT 
                pedido.id,
                pedido.id as num_pedido, 
                pedido.status, 
                pedido.id_paciente, 
                paciente.nome_fantasia as descr_paciente, 
                GROUP_CONCAT(TRIM(convenio.descr) SEPARATOR '@') as descr_convenio, 
                CASE WHEN pedido.obs = 'sistema antigo' THEN pedido.created_by ELSE prof_exa.nome_fantasia END AS descr_prof_exa, 
                users.name as created_by, 
                pedido.created_at, 
                pedido.data_validade, 
                pedido.total,
                0 as sistema_antigo
            FROM 
                `pedido` 
                left join `pessoa` as `paciente` on `paciente`.`id` = `pedido`.`id_paciente` 
                left join `pessoa` as `prof_exa` on `prof_exa`.`id` = `pedido`.`id_prof_exa` 
                left join `convenio` on `convenio`.`id` = `pedido`.`id_convenio` 
                left join `users` on `users`.`id` = `pedido`.`created_by` 
            WHERE 
            `pedido`.`id_emp` = ". getEmpresa() ."
            AND (
                `pedido`.`lixeira` = 0 
                OR `pedido`.`lixeira` IS NULL
            )
            GROUP BY pedido.id
            LIMIT 100";
        //return $query;
        $pedidos = DB::select(DB::raw($query));

        $convenios = DB::table('convenio')
                    ->where('quem_paga', 'E')
                    ->where('id_emp', getEmpresa())
                    ->get();
        
        $tabela_precos = DB::table('tabela_precos')
                    ->where('id_emp', getEmpresa())
                    ->get();

        $contas_bancarias = DB::table('contas_bancarias')
                            ->select('contas_bancarias.id', 'contas_bancarias.titular')
                            ->where('contas_bancarias.id_emp', getEmpresa())
                            ->get();

        return view('pedido', compact('pedidos', 'convenios', 'tabela_precos', 'contas_bancarias'));
    }

    public function salvar(Request $request) {
        $id_tabela_preco = DB::table('convenio')->where('id', 1)->value('id_tabela_preco');
        $num_pedido = DB::table('pedido')->where('id_emp', getEmpresa())->max('num_pedido');
        if ($num_pedido == null) $num_pedido = 1;
        else                     $num_pedido = $num_pedido + 1;

        if ($request->id == 0) {
            $pedido = new pedido;
            $pedido->num_pedido = $num_pedido;
        } else {
            $pedido = pedido::find($request->id);
        }
        if (getCaixa()) $pedido->id_caixa = getCaixa()->id;
        else            $pedido->id_caixa = 0;

        $pedido->id_emp = getEmpresa();
        $pedido->id_paciente = $request->id_paciente;
        $pedido->id_convenio = $request->id_convenio;
        $pedido->id_prof_exa = $request->id_profissional_exa;
        $pedido->num_pedido = $num_pedido;
        $pedido->data = date('Y-m-d');
        $pedido->hora = date('H:i:s');
        $pedido->data_validade = $request->data_validade;
        $pedido->consultor = Pessoa::find($request->id_profissional_exa)->nome_fantasia;
        
        $pedido->status = $request->status;
        $pedido->obs = $request->obs;
        $pedido->tipo_forma_pag = $request->tipo_forma_pag;
        if ($request->id_agendamento && sizeof($request->planos) == 1) {
            $pedido->tipo_contrato = 'P';
            $pedido->data = Agenda::find($request->id_agendamento)->data;
            $pedido->data_validade = Agenda::find($request->id_agendamento)->data;
        }
            
        else $pedido->tipo_contrato = 'N';
        $pedido->created_by = Auth::user()->id;
        $pedido->save();

        $total = 0;
        DB::table('pedido_servicos')->where('id_pedido', $pedido->id)->delete();

        $val_aux = 0;
        foreach($request->formas_pag as $forma_pag){
            $forma_pag = (object) $forma_pag;
            $val_aux += $forma_pag->forma_pag_valor;
        }
        $pedido->total = $val_aux - $request->vtroco;
        

        DB::table('pedido_parcela')
        ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id', 'pedido_parcela.id_pedido_forma_pag')
        ->where('pedido_forma_pag.id_pedido', $pedido->id)
        ->delete();
        DB::table('pedido_forma_pag')->where('id_pedido', $pedido->id)->delete();
        
        $valor_dinheiro = 0;
        foreach ($request->formas_pag as $forma_pag) {
            $forma_pag = (object) $forma_pag;
            $dias_entre_parcela = DB::table("forma_pag")->where('id', $forma_pag->id_forma_pag)->value('dias_entre_parcela');
            $pedido_forma_pag = new PedidoFormaPag;
            $pedido_forma_pag->id_emp = getEmpresa();
            $pedido_forma_pag->id_pedido = $pedido->id;
            $pedido_forma_pag->id_forma_pag = $forma_pag->id_forma_pag;
            $pedido_forma_pag->id_financeira = $forma_pag->id_financeira;
            $pedido_forma_pag->num_total_parcela = $forma_pag->parcela;
            $pedido_forma_pag->valor_total = $forma_pag->forma_pag_valor;
            $pedido_forma_pag->tipo = $request->tipo_forma_pag;
            if ($forma_pag->id_forma_pag == 2) $pedido_forma_pag->troco = $request->vtroco;
            $pedido_forma_pag->save();

            if ($forma_pag->id_forma_pag == 2){
                $valor_dinheiro;
            }

            if($forma_pag->id_forma_pag == 101) {
                $pessoa_aux = Pessoa::find($pedido->id_paciente);
                $pessoa_aux->creditos = $pessoa_aux->creditos - $forma_pag->forma_pag_valor;
                $pessoa_aux->save();


                $mov_credito = new MovCredito;
                $mov_credito->id_pedido = $pedido->id;
                $mov_credito->id_pessoa = $pedido->id_paciente;
                $mov_credito->valor = $forma_pag->forma_pag_valor;
                $mov_credito->tipo_transacao = 'S';
                $mov_credito->planos = "Compra de Contrato";
                $mov_credito->saldo = $pessoa_aux->creditos;
                $mov_credito->created_by = Auth::user()->name;
                $mov_credito->save();
            }


            $valor_parcela = $forma_pag->forma_pag_valor / $forma_pag->parcela;
            if ($valor_parcela * $forma_pag->parcela < $forma_pag->forma_pag_valor) {
                $acrescimo = $forma_pag->forma_pag_valor - ($valor_parcela * $forma_pag->parcela);
            } else {
                $acrescimo = 0;
            }

            for ($i = 0; $i < $forma_pag->parcela; $i++) {
                $pedido_parcela = new PedidoParcela;
                $pedido_parcela->id_emp = getEmpresa();
                $pedido_parcela->id_pedido_forma_pag = $pedido_forma_pag->id;
                $pedido_parcela->parcela = ($i + 1);
                if ($acrescimo > 0 && $i == 0) $pedido_parcela->valor = $valor_parcela + $acrescimo;
                else                           $pedido_parcela->valor = $valor_parcela;
                $pedido_parcela->vencimento = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . ($dias_entre_parcela * $i) . ' days'));
                $pedido_parcela->save();


                // // FINANCEIRO \\
                $tituloreceber = new TitulosReceber;
                if (getCaixa()) $tituloreceber->id_caixa = getCaixa()->id;
                else            $tituloreceber->id_caixa = 0;
                $tituloreceber->id_financeira = $forma_pag->id_financeira;
                $tituloreceber->descr = 'Venda de contrato';
                $tituloreceber->origem = 'Pedido';
                $tituloreceber->parcela = ($i + 1);
                $tituloreceber->id_forma_pag = $pedido_forma_pag->id_forma_pag;
                $tituloreceber->forma_pag = FormaPag::find($pedido_forma_pag->id_forma_pag)->descr;
                $tituloreceber->id_pedido = $pedido->id;
                $tituloreceber->id_pedido_forma_pag = $pedido_forma_pag->id;
                $tituloreceber->ndoc = $pedido->id;
                $tituloreceber->id_pessoa = $request->id_paciente;
                $tituloreceber->d_entrada = $pedido->data;
                $tituloreceber->h_entrada = $pedido->hora;
                $tituloreceber->d_emissao = $pedido->data;

                if ($i == 0) {
                    $tituloreceber->d_vencimento = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . ($dias_entre_parcela * $i) . ' days'));
                } else {
                    $d_vencimento = date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . ($dias_entre_parcela * $i) . ' days'));
                    if ($forma_pag->id_financeira <> 0) $tituloreceber->d_vencimento = date('Y-m-d', strtotime($d_vencimento . ' + ' . Financeira::find($forma_pag->id_financeira)->prazo));
                    else                                $tituloreceber->d_vencimento = $d_vencimento;
                }

                // DINHEIRO, TRANSFERENCIA E PIX \\
                if ($pedido_forma_pag->id_forma_pag == 2 || $pedido_forma_pag->id_forma_pag == 4 || $pedido_forma_pag->id_forma_pag == 5){
                    $tituloreceber->d_pago = $pedido->data;
                    $tituloreceber->h_pago = $pedido->hora;
                    $tituloreceber->id_conta = $forma_pag->id_financeira;
                    $tituloreceber->pago = 'S';
                    $tituloreceber->pago_por = Auth::user()->id_profissional;
                    $tituloreceber->pago_por_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    if ($acrescimo > 0 && $i == 0) $tituloreceber->valor_total = $valor_parcela + $acrescimo;
                    else                           $tituloreceber->valor_total = $valor_parcela;
                    if ($acrescimo > 0 && $i == 0) $tituloreceber->valor_total_pago = $valor_parcela + $acrescimo;
                    else                           $tituloreceber->valor_total_pago = $valor_parcela;
                }

                // CARTAO \\
                else if ($pedido_forma_pag->id_forma_pag == 1 || $pedido_forma_pag->id_forma_pag == 3){
                    // ****************************************** \\
                    // Dando Baixa no título em nome do associado \\
                    $tituloreceber->d_pago = $pedido->data;
                    $tituloreceber->h_pago = $pedido->hora;
                    $tituloreceber->pago = 'S';
                    $tituloreceber->pago_por = Auth::user()->id_profissional;
                    $tituloreceber->pago_por_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    if ($acrescimo > 0 && $i == 0) $tituloreceber->valor_total = $valor_parcela + $acrescimo;
                    else                           $tituloreceber->valor_total = $valor_parcela;
                    if ($acrescimo > 0 && $i == 0) $tituloreceber->valor_total_pago = $valor_parcela + $acrescimo;
                    else                           $tituloreceber->valor_total_pago = $valor_parcela;
                    $tituloreceber->movimento = 'N';
                    $tituloreceber->created_by = Auth::user()->id_profissional;
                    $tituloreceber->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $tituloreceber->updated_by = Auth::user()->id_profissional;
                    $tituloreceber->updated_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;

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
                    $tituloreceber->id_forma_pag        = $pedido_forma_pag->id_forma_pag;
                    $tituloreceber->forma_pag           = FormaPag::find($pedido_forma_pag->id_forma_pag)->descr;
                    $tituloreceber->id_pedido           = $pedido->id;
                    $tituloreceber->id_pedido_forma_pag = $pedido_forma_pag->id;
                    $tituloreceber->ndoc                = $pedido->id;
                    $tituloreceber->id_pessoa           = $request->id_paciente;
                    $tituloreceber->d_entrada           = $pedido->data;
                    $tituloreceber->h_entrada           = $pedido->hora;
                    $tituloreceber->d_emissao           = $pedido->data;
                    if ($i == 0) {
                        $tituloreceber->d_vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento)));
                    } 
                    else {
                        if ($forma_pag->id_financeira <> 0) $tituloreceber->d_vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento))) . ' + ' . (($i + 1) * Financeira::find($forma_pag->id_financeira)->prazo) .  ' days'));
                        else                                $tituloreceber->d_vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento))) . ' + ' . (($i + 1) * 30) .  ' days'));
                    }
                    if ($forma_pag->id_financeira <> 0) {
                        $aux_taxa = DB::table('financeira_taxas')
                                    ->select('taxa')
                                    ->where('id_financeira', $forma_pag->id_financeira)
                                    ->where('max_parcela','>=', $forma_pag->parcela)
                                    ->orderBy('max_parcela')
                                    ->first();

                        if ($aux_taxa) {
                            $taxa = $aux_taxa->taxa;
                        }
                        else $taxa = Financeira::find($forma_pag->id_financeira)->taxa_padrao;
                        $tituloreceber->valor_total      = $valor_parcela - (($valor_parcela * $taxa)/100);
                        $tituloreceber->taxa_financeira  = $taxa;
                    }
                    else {
                        $tituloreceber->valor_total      = $valor_parcela;
                    }
                }
                else {
                    $tituloreceber->valor_total = $valor_parcela;
                }
                $tituloreceber->created_by = Auth::user()->id_profissional;
                $tituloreceber->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                $tituloreceber->updated_by = Auth::user()->id_profissional;
                $tituloreceber->updated_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;

                $tituloreceber->obs = 'Compra de contrato';
                $tituloreceber->save();
                



                if ($pedido_forma_pag->id_forma_pag == 4 || $pedido_forma_pag->id_forma_pag == 5){
                    // MOV CONTA \\
                    $mov_conta = new MovConta;
                    $mov_conta->id_conta         = $forma_pag->id_financeira;
                    $mov_conta->id_titulo        = $tituloreceber->id;
                    $mov_conta->tipo             = 'E';
                    $mov_conta->valor            = $tituloreceber->valor_total;
                    $mov_conta->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $mov_conta->desconto         = 0;
                    $mov_conta->acrescimo        = 0;
                    $mov_conta->save();
                }
                

            }

            if (getCaixa()) {
                // CAIXA \\
                $caixa = Caixa::find(getCaixa()->id);
    
                $caixa_mov = new CaixaMov;
                $caixa_mov->id_caixa = $caixa->id;
                $caixa_mov->id_pedido = $pedido->id;
                $caixa_mov->descr = "Venda de Contrato";
                $caixa_mov->id_forma_pag = $forma_pag->id_forma_pag;
                $caixa_mov->valor = $forma_pag->forma_pag_valor;
                $caixa_mov->tipo  = "E";
                $caixa_mov->data = $pedido->data;
                $caixa_mov->hora = $pedido->hora;
                $caixa_mov->created_by = Auth::user()->id_profissional;
                $caixa_mov->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                $caixa_mov->saldo_anterior = $caixa->valor;
    
                if ($forma_pag->id_forma_pag == 2) $caixa_mov->saldo_resultante = ($caixa->valor - $request->vtroco + $val_aux);
                else $caixa_mov->saldo_resultante = $caixa_mov->saldo_anterior;
                $caixa_mov->save();
    
                $caixa->valor = $caixa_mov->saldo_resultante;
                $caixa->save();

                // return $caixa_mov->id; 
                // MOV CONTA \\
                $conta_caixa = DB::table('contas_bancarias')
                                ->where('lixeira', 'N')
                                ->where('caixa', 'S')
                                ->where('id_caixa', $caixa->id)
                                ->first();
                $mov_conta = new MovConta;
                $mov_conta->id_conta         = $conta_caixa->id;
                $mov_conta->id_titulo        = $caixa_mov->id;
                $mov_conta->tipo             = 'E';
                $mov_conta->valor            = $tituloreceber->valor_total;
                $mov_conta->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                $mov_conta->desconto         = 0;
                $mov_conta->acrescimo        = 0;
                $mov_conta->save();
                
            }
        }
        $contrato = 'N';
        foreach($request->planos as $plano){
            $plano = (object) $plano;

            $aux = DB::table('tabela_precos_vigencia')
                   ->where('id_tabela_preco', $plano->id_plano)
                   ->where('de', '<=', $plano->qtd)
                   ->where('ate', '>=', $plano->qtd)
                   ->first();
            
            if ($aux) {
                if      ($aux->vigencia == 30) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +1 month'));
                else if ($aux->vigencia == 60) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +2 month'));
                else if ($aux->vigencia == 90) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +3 month'));
                else if ($aux->vigencia == 180)$data_validade = date('Y-m-d', strtotime($pedido->data . ' +6 month'));
                else if ($aux->vigencia == 360)$data_validade = date('Y-m-d', strtotime($pedido->data . ' +1 year'));
                else                           $data_validade = $pedido->data;
            }
            else {
                $vigencia = TabelaPrecos::find($plano->id_plano);

                if      ($vigencia->vigencia == 30) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +1 month'));
                else if ($vigencia->vigencia == 60) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +2 month'));
                else if ($vigencia->vigencia == 90) $data_validade = date('Y-m-d', strtotime($pedido->data . ' +3 month'));
                else if ($vigencia->vigencia == 180)$data_validade = date('Y-m-d', strtotime($pedido->data . ' +6 month'));
                else if ($vigencia->vigencia == 360)$data_validade = date('Y-m-d', strtotime($pedido->data . ' +1 year'));
                else                                $data_validade = $pedido->data;
            }

            $data1 = new \DateTime($pedido->data);
            $data2 = new \DateTime($data_validade);
            $intervalo = $data1->diff($data2);

            if (TabelaPrecos::find($plano->id_plano)->max_atv_semana != 0) $quantidade_total = intval((($intervalo->days/7) * intval(TabelaPrecos::find($plano->id_plano)->max_atv_semana)) + 0.99);
            else $quantidade_total = TabelaPrecos::find($plano->id_plano)->max_atv;

            $pedido_planos = new PedidoPlanos;
            $pedido_planos->id_emp          = getEmpresa();
            $pedido_planos->id_pedido       = $pedido->id;
            $pedido_planos->id_plano        = $plano->id_plano;
            $pedido_planos->data_validade   = $data_validade;
            $pedido_planos->qtde            = $plano->qtd;
            $pedido_planos->qtd_total       = $quantidade_total;
            $pedido_planos->qtd_original    = (TabelaPrecos::find($plano->id_plano)->max_atv);
            $pedido_planos->valor           = $plano->valor;
            $pedido_planos->valor_original  = $plano->valor_original;
            $pedido_planos->save();
            if (TabelaPrecos::find($plano->id_plano)->contrato == 'S') $contrato = 'S';
        }
        if ($request->id_agendamento) {
            $agendamento = Agenda::find($request->id_agendamento);
            $agendamento->id_pedido = $pedido->id;
            $agendamento->id_tabela_preco = DB::table('pedido_planos')
                                            ->where('id_pedido', $pedido->id)
                                            ->value("id_plano");
            $agendamento->save();
            if ($agendamento->id_encaminhamento) $pedido->id_encaminhamento = $agendamento->id_encaminhamento;
            $pedido->id_agendamento = $request->id_agendamento;
        }

        $pedido->contrato = $contrato;
        $pedido->save();
        if ($request->d_sup > 0) {
            $data = new Desconto;
            $data->id_supervisor = $request->d_sup;
            $data->id_pedido = $pedido->id;
            $data->motivo = $request->d_motivo;
            $data->save();
        }
        if ($request->enc_id_de > 0) {
            $data = new Encaminhamento;
            $data->id_de = $request->enc_id_de;
            $data->id_paciente = $pedido->id_paciente;
            $data->id_especialidade = $request->enc_id_especialidade;
            $data->id_cid = $request->enc_id_cid;
            $data->data = date('Y-m-d', strtotime(str_replace("/", "-", $request->enc_data)));
            $data->save();
            $pedido->id_encaminhamento = $data->id;
            $pedido->save();
        }
        return json_encode($pedido);
    }

    public function mudar_status(Request $request) {
        try {
            $pedido = pedido::find($request->id);
            $pedido->status = $request->status;
            $pedido->save();
    
            return json_encode($pedido);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $pedido = Pedido::find($request->id);
            $pedido->lixeira = true;
            $pedido->save();
            DB::statement("update agenda set lixeira = 1 where id_pedido = ".$request->id);

            $formas_pag = DB::select(
                DB::raw("
                    select * from pedido_forma_pag where id_forma_pag = 2 AND id_pedido = " . $request->id
                )
            );
            if (sizeof($formas_pag) > 0) {
                $caixa_mov = DB::table('caixa_mov')
                         ->where('caixa_mov.id_pedido', $request->id)
                         ->where('caixa_mov.id_forma_pag', 2)
                         ->get();
                
                if (sizeof($caixa_mov) > 0) {
                    $caixa = Caixa::find($caixa_mov[0]->id_caixa);
    
                    $caixa_mov = new CaixaMov;
                    $caixa_mov->id_caixa = $caixa->id;
                    $caixa_mov->id_pedido = $pedido->id;
                    $caixa_mov->descr = "Exclusão de Contrato";
                    $caixa_mov->id_forma_pag = $formas_pag[0]->id_forma_pag;
                    $caixa_mov->valor = $formas_pag[0]->valor_total;
                    $caixa_mov->tipo  = "R";
                    $caixa_mov->data = $pedido->data;
                    $caixa_mov->hora = $pedido->hora;
                    $caixa_mov->created_by = Auth::user()->id_profissional;
                    $caixa_mov->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $caixa_mov->saldo_anterior = $caixa->valor;
        
                    $caixa_mov->saldo_resultante = $caixa_mov->saldo_anterior - $formas_pag[0]->valor_total;
                    $caixa_mov->save();
        
                    $caixa->valor = $caixa_mov->saldo_resultante;
                    $caixa->save();
                }
            }

            return $request->id;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    
    public function atividades_por_pessoa($id_paciente) {
            // $pedido = DB::table('pedido')
            //         -> select(
            //             'pedido.id',
            //             'pedido.data_validade',
            //             'pedido.lixeira',
            //             'pedido.status'
            //         )
                    
            //         ->where("data_validade", ">", "sysdatetime")
            //         ->where('lixeira', 0)
            //         ->where('status', 'F')
            //         ->get();

            $pedidos = DB::table('pedido')
                      ->selectRaw('Group_Concat(pedido.id) AS ids')
                      ->where('lixeira', 0)
                      ->where('status', 'F')
                      ->where('id_paciente', $id_paciente)
                      ->groupBy('id_paciente')
                      ->value('ids');
            $pedidos = "(" . $pedidos . ")";

            if ($pedidos == '()'){
                $data = new \StdClass();
                $data->disponivel = 0;
                $data->agendados = 0;
                $data->total = 0;
            } else {
                $max_atv = DB::table('pedido_planos')
                       ->select(DB::raw('SUM(pedido_planos.qtde * pedido_planos.qtd_total) AS total'))
                        ->leftjoin('pedido', 'pedido.id', 'pedido_planos.id_pedido')
                        ->whereRaw('pedido_planos.id_pedido in '. $pedidos)
                        ->groupBy('id_paciente')
                        ->value('total');
                
                $agendados = DB::table('agenda')
                             ->whereRaw('agenda.id_pedido in '. $pedidos ."AND 
                                         lixeira = 0 AND
                                         status = 'A'")
                             ->count();
    
                $total_consu = DB::table('agenda')
                                ->whereRaw('agenda.id_pedido in '. $pedidos ."AND 
                                            lixeira = 0 AND
                                            status in ('A', 'F')")
                                ->count();
                
                $data = new \StdClass();
                $data->disponivel = ($max_atv - $total_consu);
                $data->agendados = $agendados;
                $data->total = ($data->disponivel + $data->agendados);
            }
                    
            return json_encode($data);
    }







    public function imprimir($id, $antigo) {
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
                            'pedido.data',
                            'pedido.assinado'
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
                            "old_contratos.datainicial AS data",
                            DB::raw("(select 'N') AS assinado")
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'old_contratos.pessoas_id')
                        ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                        ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_planopagamento')
                        ->where('old_contratos.id', $id)
                        ->first();

            $planos = DB::table('pedido_planos')
                      ->select('tabela_precos.descr    AS descr',
                               'tabela_precos.vigencia AS vigencia',
                               'pedido_planos.valor    AS valor',
                               DB::raw("SUM(".
                                            "CASE WHEN (agenda.id is not NULL AND agenda.status in ('F', 'A') AND agenda.lixeira = 0) THEN (select 1)".
                                            "ELSE (select 0) END) AS atv_consu"),
                               DB::raw("(pedido_planos.qtd_total * pedido_planos.qtde) AS max_atv"))
                      ->join('tabela_precos', 'tabela_precos.id', 'id_plano')
                      ->leftjoin('agenda', function($sql){
                            $sql->on('agenda.id_pedido', '=', 'pedido_planos.id_pedido')
                                ->on('agenda.id_tabela_preco', '=', 'pedido_planos.id_plano');
                      })
                      ->where('pedido_planos.id_pedido', $id)
                    //   ->where(function($sql) {
                    //     $sql->where('agenda.lixeira', 0)
                    //         ->orWhere('agenda.lixeira', 'is',null);
                    //   })

                      ->groupBy('tabela_precos.descr',
                                'tabela_precos.vigencia',
                                'pedido_planos.valor',
                                'pedido_planos.qtd_total',
                                'pedido_planos.qtde')
                      ->get();
        
            $planos_old = DB::table('old_atividades')
                      ->select('old_modalidades.descr  AS  descr',
                               'old_atividades.periodo_dias     AS vigencia',
                               DB::raw("(old_atividades.valor_cardapio * old_atividades.qtd_ini) as valor"),
                               DB::raw('(old_atividades.qtd_ini - old_atividades.qtd) as atv_consu'),
                               "old_atividades.qtd_ini AS max_atv")
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
                
                foreach($pedido_formas_pag AS $pag) {
                    $pag->parcelas = DB::table('pedido_parcela')
                                    ->where('id_pedido_forma_pag', $pag->id)
                                    ->get();
                }
            // }       
            // if ($pedido_header <> 'sistema_antigo'){
                $parcelas = DB::table('old_finanreceber')
                                    ->select('old_finanreceber.parcela',
                                             'old_plano_pagamento.descr AS descr_forma_pag',
                                             'old_finanreceber.valor AS valor',
                                             'old_finanreceber.datavenc AS vencimento',
                                             'old_finanreceber.id_planopagamento')       
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
            $path = database_path('empresa') . '/' . getEmpresa() . '.png';
            if (file_exists($path)) {
                $emp_logo = base64_encode(file_get_contents($path));
            }
            if ($antigo == 0){
                return view('.reports.impresso_pedido', compact('pedido_header', 'pedido_formas_pag', 'emp_logo', 'planos', 'antigo', 'parcelas'));
            }
            else {
                $pedido_header = $pedido_header_old;
                $planos = $planos_old;
                return view('.reports.impresso_pedido', compact('pedido_header', 'parcelas', 'emp_logo', 'planos', 'antigo', 'pedido_formas_pag'));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa, $antigo) {     
        $data = new \StdClass;

        if ($antigo == 0) {
            $pedidos = DB::select(
                DB::raw("
                    select
                        pedido.id,
                        pedido.id_emp,
                        empresa.descr as descr_emp,
                        pedido.data,
                        pedido.hora,
                        pedido.total,
                        pedido.data_validade,
                        pedido.status,
                        pedido.assinado,
                        auxContrato.signed_file AS signed_url
                    from
                        pedido
                        left join empresa on empresa.id = pedido.id_emp
                        left join (
                            select 
                                id_pedido, 
                                MAX(signed_file)  as signed_file
                            from 
                                contrato
                            group by 
                                contrato.id_pedido
                        ) as auxContrato on auxContrato.id_pedido = pedido.id
                    where
                        (
                            pedido.lixeira is null or
                            pedido.lixeira = 0
                        ) AND pedido.id_paciente = ". $id_pessoa ."
                    order by pedido.data DESC
                ")
            );
        

            $total_atividades = array();
            $atividades_consumidas = array();
            $contratos = array();
            foreach($pedidos AS $pedido){
                $total = DB::table('pedido_planos')
                            ->selectRaw("SUM(pedido_planos.qtd_total * pedido_planos.qtde) as total")
                            ->where('pedido_planos.id_pedido', $pedido->id)
                            ->value('total');
                $consumidas = sizeof(DB::table('agenda')
                                ->where('id_pedido',$pedido->id)
                                ->where('agenda.status', 'F')
                                ->where('agenda.lixeira', 0)
                                ->get());
                
                array_push($total_atividades, $total);
                array_push($atividades_consumidas, $consumidas);
                
                $aux_pedido_planos = DB::table('pedido_planos')
                                        ->select('tabela_precos.contrato')
                                        ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                        ->where('pedido_planos.id_pedido', $pedido->id)
                                        ->get();
                $c_aux = "N";
                foreach($aux_pedido_planos AS $plano) {
                    if ($plano->contrato == "S") $c_aux = "S";
                }
                array_push($contratos, $c_aux);
            }

            $data->pedidos = $pedidos;
            $data->total_atividades = $total_atividades;
            $data->atividades_consumidas = $atividades_consumidas;
            $data->contratos = $contratos;
           
        } else {
            $pedidos_antigos = DB::table('old_contratos')
                           ->select('old_contratos.id',
                                    'old_contratos.datainicial AS data',
                                    'old_contratos.horainicial AS hora',
                                    'old_contratos.valor_contrato AS total',
                                    DB::raw("CONCAT(old_contratos.datafinal, ' ', old_contratos.horafinal) AS data_validade" ),
                                    DB::raw("CASE WHEN (old_contratos.situacao = 1) THEN (select 'F')
                                                  ELSE old_contratos.situacao END AS status"),
                                    DB::raw("(select 'N') AS assinado"))
                            ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                            ->where('old_contratos.pessoas_id', $id_pessoa)
                            ->where('old_contratos.situacao', 1)
                            // ->where('old_contratos.tipo_contrato', '')
                            ->groupBy("old_contratos.id",
                                      "old_contratos.id_emp",
                                      "old_contratos.datainicial",
                                      "old_contratos.horainicial",
                                      "old_contratos.valor_contrato",
                                      "old_contratos.datafinal",
                                      "old_contratos.horafinal",
                                      "old_contratos.situacao")
                            ->orderBy('old_contratos.datafinal', 'DESC')
                            ->take(40)
                            ->get();

            $total_atividades_ant = array();
            $atividades_consumidas_ant = array();
            
            foreach($pedidos_antigos AS $pedido){
                $total_old = DB::table('old_atividades')
                ->selectRaw('SUM(qtd_ini) as total')
                ->where('id_contrato', $pedido->id)
                ->value('total');

                $consumidas_old = DB::table('old_atividades')
                ->selectRaw('(SUM(qtd_ini) - SUM(qtd)) as total')
                ->where('id_contrato', $pedido->id)
                ->value('total');

                array_push($total_atividades_ant, $total_old);
                array_push($atividades_consumidas_ant, $consumidas_old);
            }

                $data->pedidos = $pedidos_antigos;
                $data->total_atividades = $total_atividades_ant;
                $data->atividades_consumidas = $atividades_consumidas_ant;
                
        }
        return json_encode($data);
    }

    public function adicionar_plano(Request $request){
        try{
            $plano = new PedidoPlanos;

            $plano->id_emp          = getEmpresa();
            $plano->id_pedido       = $request->id;
            $plano->id_plano        = $request->id_plano;
            $plano->id_profissional = $request->id_profissional; 
            $plano->saldo           = getMaxAtvSemana($request->id_plano);   
            $plano->save();

            return $plano;
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function listar_planos($id_pedido, $id_pessoa){


        
        $data = new \StdClass;

        $data->pedido_plano = DB::table('tabela_precos')
                            ->where('id', $id_pedido)
                            ->first();

        $regra_associado = DB::table('associados_regra')
                            ->where('ativo', true)
                            ->value('dias_pos_fim_contrato');
 
         $associado = DB::table('pedido')
                     ->where('id_paciente', $id_pessoa)
                     ->where(function($sql) {
                         $sql->where('pedido.lixeira', false)
                             ->orWhere('pedido.lixeira', null);
                     })
                     ->orderBy('data_validade', 'DESC')
                     ->first(); 
         if (!$associado){
            $data->associado = 'N';
         }else{
             if (date($associado->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')){
                $data->associado = 'S';
             }else $data->associado = 'N';
         }
                            
        return json_encode($data);
    }
    public function listar_planos_pessoa($id_pedido){
        $pedido_planos = DB::table('pedido_planos')
                         ->select('tabela_precos.id        AS id' ,
                                  'tabela_precos.descr     AS descr',
                                  'pessoa.id               AS profissional_id',
                                  'pessoa.nome_fantasia    AS profissional',
                                  'tabela_precos.n_pessoas AS n_pessoas',
                                  'tabela_precos.valor     AS valor')
                        ->where('pedido_planos.id_pedido', $id_pedido)
                        ->join('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                        ->join('pessoa'       , 'pessoa.id'       , 'pedido_planos.id_profissional')
                        ->get();
        return json_encode($pedido_planos);
    }


    public function deletar_plano($id) {
        $aux = PedidoPlanos::find($id)->delete();
    }
    public function limpar_planos(Request $request){
        $pedido = Pedido::find($request->id);
        if ($pedido->status = '*'){
            DB::table('pedido_planos')->where('id_pedido', $request->id)->delete();
            $pedido->delete();
        }
        
    }

    public function listar_contratos_pessoa($id, $bEdita, $data) {
        $pedidos = DB::select(
            DB::raw("
            Select
                descr,
                id,
                data,
                max_atv_semana,
                QTD_REST
            FROM
                (
                    SELECT 
                        GROUP_CONCAT(DISTINCT tabela_precos.descr) as descr, 
                        pedido.id, 
                        pedido.data,
                        MAX(tabela_precos.max_atv_semana) as max_atv_semana ,
                        (
                            SUM(pedido_planos.qtd_total * pedido_planos.qtde)
                            -
                            (SUM(
                            CASE WHEN (agenda.id is not NULL AND agenda.status in ('F', 'A') AND agenda.lixeira = 0) THEN (select 1)
                                ELSE (select 0) END))
                        ) AS QTD_REST
                    FROM 
                        pedido 
                        left join pedido_planos  on  pedido_planos.id_pedido = pedido.id 
                        left join tabela_precos  on  tabela_precos.id = pedido_planos.id_plano
                        left join agenda         on  agenda.id_pedido = pedido_planos.id_pedido AND
                                                    agenda.id_tabela_preco = pedido_planos.id_plano
                    where 
                        pedido.id_paciente = ". $id ." 
                        and `pedido`.`data_validade` >= '". $data ."'
                        and pedido.lixeira = 0
                        and `tipo_contrato` <> 'P' 
                        and 
                        (
                            (agenda.id is null or agenda.lixeira = 1)
                            OR
                            (agenda.status in ('A', 'F') and agenda.lixeira = 0)
                        )

                    group by 
                        pedido.data, 
                        pedido.id
                ) AS aux
            ")
        );
        if ($bEdita <> 'false' || $bEdita <> 'false') {
            return $pedidos;
        }

        $array = array();
        foreach ($pedidos AS $pedido) {
            $aux_planos = DB::table("pedido_planos")
                            ->select("tabela_precos.descr     AS descr",
                                    "tabela_precos.id        AS id",
                                    "pedido_planos.id_pedido AS id_contrato",
                                    DB::raw('(pedido_planos.qtde * pedido_planos.qtd_total) AS agendaveis'),
                                    DB::raw("SUM(".
                                                "CASE WHEN (agenda.lixeira = 0 AND (agenda.status = 'F' or agenda.status = 'A')) THEN (select 1)".
                                                "ELSE (select 0) END) AS agendados"))
                            ->leftjoin("tabela_precos", 'tabela_precos.id', 'pedido_planos.id_plano')
                            ->leftjoin("pedido",        'pedido.id',        'pedido_planos.id_pedido')
                            ->leftjoin('agenda', function($sql){
                                $sql->on('agenda.id_pedido', '=', 'pedido_planos.id_pedido')
                                    ->on('agenda.id_tabela_preco', '=', 'pedido_planos.id_plano');
                        })
                            ->where('pedido_planos.id_pedido', $pedido->id)
                            // ->where(function($sql){
                            //     $sql->where('tabela_precos.pre_agendamento', 0)
                            //         ->orWhere('tabela_precos.pre_agendamento', null);
                            // })
                            // ->where('pedido_planos.data_validade', '>=', date('Y-m-d'))
                            ->where('pedido.tipo_contrato', 'N')
                            ->groupBy("tabela_precos.descr",
                                    "tabela_precos.id",
                                    "pedido_planos.id_pedido",
                                    'pedido_planos.qtde',
                                    "pedido_planos.qtd_total",
                                        'tabela_precos.valor')
                            ->get();
            $cont = 0;
            foreach($aux_planos As $plano) {
                $cont += ($plano->agendaveis - $plano->agendados);
            }
            // return $cont;
            if ($cont > 0){
                array_push($array, $pedido);
            }
        }

        return $array;
    }

    public function listar_planos_pedido(Request $request) {
        return DB::table("pedido_planos")
                        ->select("tabela_precos.descr     AS descr",
                                 "tabela_precos.id        AS id",
                                 "pedido_planos.id_pedido AS id_contrato",
                                 DB::raw('(pedido_planos.qtde * pedido_planos.qtd_total) AS agendaveis'),
                                 DB::raw("SUM(".
                                            "CASE WHEN (agenda.lixeira = 0 AND (agenda.status = 'F' or agenda.status = 'A')) THEN (select 1)".
                                            "ELSE (select 0) END) AS agendados"))
                        ->leftjoin("tabela_precos", 'tabela_precos.id', 'pedido_planos.id_plano')
                        ->leftjoin("pedido",        'pedido.id',        'pedido_planos.id_pedido')
                        ->leftjoin('agenda', function($sql){
                            $sql->on('agenda.id_pedido', '=', 'pedido_planos.id_pedido')
                                ->on('agenda.id_tabela_preco', '=', 'pedido_planos.id_plano');
                      })
                        ->where('pedido_planos.id_pedido', $request->id_contrato)
                        // ->where(function($sql){
                        //     $sql->where('tabela_precos.pre_agendamento', 0)
                        //         ->orWhere('tabela_precos.pre_agendamento', null);
                        // })
                        // ->where('pedido_planos.data_validade', '>=', date('Y-m-d'))
                        ->where('pedido.tipo_contrato', 'N')
                        ->groupBy("tabela_precos.descr",
                                  "tabela_precos.id",
                                  "pedido_planos.id_pedido",
                                  'pedido_planos.qtde',
                                  "pedido_planos.qtd_total",
                                    'tabela_precos.valor')
                        ->get();

    }
    public function montar_resumo(Request $request){
        $maior = 0;
        $id = 0;
        foreach($request->planos as $plano){
            $plano = (object) $plano;
            
            $aux = DB::table('tabela_precos_vigencia')
                   ->where('id_tabela_preco', $plano->id)
                   ->where('de', '<=', $plano->qtd)
                   ->where('ate', '>=', $plano->qtd)
                   ->first();
            if ($aux){
                if ($aux->vigencia > $maior) {
                    $maior = $aux->vigencia;     
                    $id    = $aux->id;
                }
            }
            else {
                $planos = TabelaPrecos::find($plano->id);

                if ($planos->vigencia > $maior) {
                    $maior = $planos->vigencia;     
                    $id    = $planos->id;
                }
            }
        }
        $aux = $maior;
        
        if      ($aux == 30)  $data_validade = date('Y-m-d', strtotime('+1 month'));
        else if ($aux == 60)  $data_validade = date('Y-m-d', strtotime('+2 month'));
        else if ($aux == 90)  $data_validade = date('Y-m-d', strtotime('+3 month'));
        else if ($aux == 180) $data_validade = date('Y-m-d', strtotime('+6 month'));
        else if ($aux == 360) $data_validade = date('Y-m-d', strtotime('+1 year'));
        else                  $data_validade = date('Y-m-d');
        
        return $data_validade;
    }
    public function gerar_id(){
        return gerar_id();
    }
    public function congelar_pedido(Request $request){
        $pedido = Pedido::find($request->id);
        $pedido->status = 'S';
        $pedido->data_congelamento = date('Y-m-d'); 
        $pedido->data_descongelar  = $request->data;
        $pedido->save();

        $pedidoPlanos = DB::table('pedido_planos')
                        ->updateOrInsert([
                            "id_pedido"         => $request->id],[
                            "data_congelamento" => date('Y-m-d'),
                            "data_descongelar"  => $request->data
                        ]);
        return 'true';
    }
    
    public function descongelar(Request $request){
        $pedido = Pedido::find($request->id);

        $data_congelamento = new \DateTime($pedido->data_congelamento);
        $data_descongelado = new \DateTime(date('Y-m-d'));

        $intervalo = $data_congelamento->diff($data_descongelado);
        $intervalo = strval($intervalo->days);


        $pedido->data_validade = date('Y-m-d', strtotime('+'. $intervalo .' days', strtotime($pedido->data_validade)));
        $pedido->status = 'F';
        $pedido->save();

        $pedidoPlanos = DB::table('pedido_planos')
                        ->updateOrInsert([
                            "id_pedido"         => $request->id],[
                            "data_congelamento" => date('Y-m-d'),
                            "data_descongelar"  => $request->data
                        ]);
        return 'true';

    }

    public function enviar_contrato_por_email (Request $request) {
        $pessoa = Pessoa::find($request->id_pessoa);

        $plano  = TabelaPrecos::find($request->id_plano);

        return view('reports.impresso_contrato', compact('pessoa', 'plano'));
    }
    public function validar($id_pessoa){
        $pessoa = Pessoa::find($id_pessoa);


        if($pessoa->nome_fantasia == '' || $pessoa->nome_fantasia == NULL ||
        $pessoa->cpf_cnpj == ''         || $pessoa->cpf_cnpj      == NULL ||
        $pessoa->rg_ie == ''            || $pessoa->rg_ie         == NULL ||
        $pessoa->endereco == ''         || $pessoa->endereco      == NULL ||
        $pessoa->numero == ''           || $pessoa->numero        == NULL ||
        $pessoa->bairro == ''           || $pessoa->bairro        == NULL ||
        $pessoa->cidade == ''           || $pessoa->cidade        == NULL ||
        $pessoa->estado == ''           || $pessoa->estado        == NULL){
            return 'false';
        }
        else return 'true';
    }
    private function unificarTP($tabela) {
        $resultado = array();
        $ids = array();
        foreach($tabela as $linha) {
            if (!in_array($linha->id, $ids)) {
                array_push($ids, $linha->id);
                array_push($resultado, $linha);
            }
        }
        return $resultado;
    }
    public function listar_planos_desc($id_pessoa, $id_convenio) {

        $empresa = getEmpresa();

        $regra_associado = DB::table('associados_regra')
                           ->where('ativo', true)
                           ->value('dias_pos_fim_contrato');

        $associado = DB::table('pedido')
                    ->select('pedido.data_validade')
                    ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                    ->where('id_paciente', $id_pessoa)
                    ->where('pedido.lixeira', 0)
                    ->where('pedido.tipo_contrato', '<>', 'P')
                    ->where('pedido.status', 'F')
                    ->where('tabela_precos.associado', 'S')
                    ->where('pedido.data_validade', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
                    ->orderBy('pedido.data_validade', 'DESC')
                    ->unionAll(
                        DB::table('old_contratos')
                        ->select('datafinal AS data_validade')
                        ->where('old_contratos.pessoas_id', $id_pessoa)
                        ->where('situacao', '1')
                        ->where('old_contratos.datafinal', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
                        ->orderBy('datafinal', 'DESC')
                    )
                    ->first();
                    

        $data = new \StdClass;
        if ($id_convenio == null || $id_convenio == 'null' || $id_convenio == '0' | $id_convenio == 0){
            $data->tabela_precos = DB::table('tabela_precos')
                                 ->select('tabela_precos.descr AS descr_tabela_preco',
                                          'tabela_precos.id                  AS id',
                                          'tabela_precos.vigencia            AS vigencia',
                                          'tabela_precos.n_pessoas           AS n_pessoas',
                                          DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" . 
                                                             "CONCAT(tabela_precos.descr,' | mensal'))".
                                                      "WHEN (tabela_precos.vigencia = 60) THEN (".
                                                              "CONCAT(tabela_precos.descr, ' | bimestral'))".
                                                          "WHEN (tabela_precos.vigencia = 90) THEN (".
                                                              "CONCAT(tabela_precos.descr, ' | trimestral'))".
                                                          "WHEN (tabela_precos.vigencia = 180) THEN (".
                                                              "CONCAT(tabela_precos.descr, ' | semestral'))".
                                                          "WHEN (tabela_precos.vigencia = 360) THEN (".
                                                              "CONCAT(tabela_precos.descr, ' | anual'))".
                                                              " ELSE '' END AS descr"),
    
                                            'tabela_precos.desconto_associados AS desconto_associados',
                                            'tabela_precos.valor               AS valor',
                                            DB::raw("(select null)               AS valor_convenio"))
                                 ->leftjoin('empresas_plano', 'empresas_plano.id_tabela_preco', 'tabela_precos.id')
                                 ->where('empresas_plano.id_emp', getEmpresa())
                                ->where('tabela_precos.lixeira', 0)
                                ->orderBy('tabela_precos.descr')
                                ->get();

        }
        else {
            $data->tabela_precos = DB::table('tabela_precos')
                                 ->select('preco_convenios_plano.id_convenio','tabela_precos.descr AS descr_tabela_preco',
                                          'tabela_precos.id                  AS id',
                                          'tabela_precos.vigencia            AS vigencia',
                                          'tabela_precos.n_pessoas           AS n_pessoas',
                                 DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" . 
                                                    "CONCAT(tabela_precos.descr,' | mensal'))".
                                               "WHEN (tabela_precos.vigencia = 60) THEN (".
                                                    "CONCAT(tabela_precos.descr, ' | bimestral'))".
                                                "WHEN (tabela_precos.vigencia = 90) THEN (".
                                                    "CONCAT(tabela_precos.descr, ' | trimestral'))".
                                                "WHEN (tabela_precos.vigencia = 180) THEN (".
                                                    "CONCAT(tabela_precos.descr, ' | semestral'))".
                                                "WHEN (tabela_precos.vigencia = 360) THEN (".
                                                    "CONCAT(tabela_precos.descr, ' | anual'))".
                                                    " ELSE '' END AS descr"),
    
                                 'tabela_precos.desconto_associados AS desconto_associados',
                                 'tabela_precos.valor               AS valor',
                                 'preco_convenios_plano.valor       AS valor_convenio')
                        ->leftjoin('preco_convenios_plano', 'preco_convenios_plano.id_tabela_preco', 'tabela_precos.id')
                        ->leftjoin('empresas_plano', 'empresas_planos.id_plano', 'tabela_precos.id')
                        ->where('empresas_plano.id_emp', getEmpresa())
                        ->where(function($sql) use ($id_convenio){
                            $sql->where('preco_convenios_plano.id_convenio', $id_convenio)
                                ->orWhere("preco_convenios_plano.id_convenio", null)
                                ->orWhere("preco_convenios_plano.id_convenio", '');
                        })
                        ->where(function($sql){
                            $sql->where('preco_convenios_plano.lixeira', 0)
                                ->orWhere('preco_convenios_plano.lixeira', null);
                        })
                        ->orderBy('tabela_precos.descr')
                        ->where('tabela_precos.lixeira', 0)
                        // ->where('tabela_precos.id', 23)
                        ->get();

        }
        $data->tabela_precos = $this->unificarTP($data->tabela_precos);
        if (!$associado){
            $data->associado = 'N';
        }else{
            if (date($associado->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')){
                $data->lista = array();
                $data->associado = 'S';
                foreach($data->tabela_precos as $plano){
                    if ($plano->desconto_associados == null) array_push($data->lista, 'N');
                    else                                     array_push($data->lista, 'S');
                }
            }else $data->associado = 'N';
        }
        if ($id_convenio === '' or $id_convenio === null or $id_convenio === 0 || $id_convenio === '0'){
            $data->convenio = 'N';
        }
        else {
            $data->valores_conv = array();
            $data->convenio = 'S';
            foreach($data->tabela_precos as $plano) {
                if ($plano->valor_convenio == null) array_push($data->valores_conv, 'N');
                else                                array_push($data->valores_conv, 'S');
            }
        }
        return json_encode($data);
        
    }
    // public function listar_planos_pedido($id_pedido) {
    //     $data = new \stdClass;
    //     $data->planos = DB::table("pedido_planos")
    //               ->select("tabela_precos.descr",
    //                        "tabela_precos.id")
    //               ->join("tabela_precos", 'tabela_precos.id', 'pedido_planos.id_plano')
    //               ->where('id_pedido', $id_pedido)
    //               ->orderby('id_pedido', 'ASC')
    //               ->get();
    //     $maxatvsemana = array();
    //     foreach($data->planos as $plano){
    //         array_push($maxatvsemana, getMaxAtvSemana($plano->id));
    //     }
    //     $data->maxatvsemana = $maxatvsemana;

    //     switch (date('w')) {
    //         case "1":
    //             $inicioSemana = $request->data;
    //             $finalSemana  = date('Y-m-d', strtotime('+5 days'));
    //             break;
    //         case "2":
    //             $inicioSemana = date("Y-m-d", strtotime('-1 day'));
    //             $finalSemana  = date("Y-m-d", strtotime('+4 days'));
    //             break;
    //         case '3':
    //             $inicioSemana = date('Y-m-d', strtotime('-2 days'));
    //             $finalSemana  = date('Y-m-d', strtotime('+3 days'));
    //             break;
    //         case '4':
    //             $inicioSemana = date('Y-m-d', strtotime('-3 days'));
    //             $finalSemana  = date('Y-m-d', strtotime('+2  day' ));
    //             break;
    //         case '5':
    //             $inicioSemana = date('Y-m-d', strtotime('-4 days'));
    //             $finalSemana  = date('Y-m-d', strtotime('+1 day' ));
    //         case '6':
    //             $inicioSemana = date('Y-m-d', strtotime('-5 days'));
    //             $finalSemana  = $request->data;    
    //     }
        
    //     $marcaveis = array();
    //     foreach($data->planos as $plano){
    //         $agendamentos = DB::table("agenda")
    //                     ->where('id_pedido',       $id_pedido)
    //                     ->where('id_tabela_preco', $plano->id)
    //                     ->where('data', '>=',      $inicioSemana)
    //                     ->where('data', '<=',      $finalSemana)
    //                     ->orderby('id_pedido', 'DESC')
    //                     ->get();
    //         array_push($marcaveis, (strval(sizeof($agendamentos))));
            
    //     }
    //     $data->marcaveis = $marcaveis;

    //     return json_encode($data);
    // }
    public function abrir_modal_conversao($id_pedido, $antigo){
        $data = new \StdClass;
        switch (intval($antigo)){
            case 1:
                $data->planos = DB::table('old_atividades')
                                ->select('old_atividades.id AS id_plano',
                                         'old_modalidades.descr AS descr_plano',
                                         'old_atividades.qtd AS qtde_restante',
                                         'old_atividades.valor_cardapio AS valor_und',
                                         DB::raw('(old_atividades.valor_cardapio * old_atividades.qtd) AS valor_total'))
                                ->join('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                                ->where('old_atividades.id_contrato', $id_pedido)
                                ->where('old_atividades.qtd', '>', '0')
                                ->get();
                $data->contrato = DB::table('old_contratos')
                                  ->select('old_contratos.datainicial AS data',
                                           'old_contratos.datafinal   AS data_validade',
                                           'old_contratos.id          AS id')
                                  ->where('id', $id_pedido)
                                  ->get();
                return json_encode($data);
                break;
            case 0:
                $data->planos = DB::table('pedido_planos')
                                ->select('pedido_planos.id AS id_plano',
                                        'tabela_precos.descr AS descr_plano',
                                        DB::raw('(pedido_planos.valor/(pedido_planos.qtde*pedido_planos.qtd_original)) AS valor_und'),
                                        DB::raw('pedido_planos.valor AS valor_total'),
                                        DB::raw('(pedido_planos.qtde*pedido_planos.qtd_total) AS qtde'),
                                        'pedido_planos.id_plano AS id')
                                ->join('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                                ->where('pedido_planos.id_pedido', $id_pedido)
                                ->get();

                $restante_ar = array();
                $aux = 0;
                foreach($data->planos AS $plano){
                    $agendamentos = DB::table('agenda')
                                    ->where('agenda.id_pedido', $id_pedido)
                                    ->where('agenda.id_tabela_preco', $plano->id)
                                    ->where(function($sql) {
                                        $sql->where('status', 'A')
                                            ->orWhere('status', 'F');
                                    })
                                    ->where('lixeira', 0)
                                    ->count();
                    if ($agendamentos >= $plano->qtde){
                        array_push($restante_ar, 0);
                    }
                    else {
                        array_push($restante_ar, ($plano->qtde - $agendamentos));
                        $aux += 1;
                    }
                } 
                $data->aux = $aux;
                $data->restantes_ar = $restante_ar;
                $data->contrato = DB::table('pedido')
                                ->select('pedido.data',
                                         'pedido.data_validade')
                                ->where('id', $id_pedido)
                                ->get();

                return json_encode($data);
                break;
            default:
                return 'erro';
                break;
        }
    }
    public function converter(Request $request){
        $pessoa = Pessoa::find($request->id_pessoa);
        $pessoa->creditos = ($pessoa->creditos + $request->valor_total);
        $pessoa->save();

        $plano_aux = '';
        if ($request->bAntigo == 1) {
            for($i = 0; $i < sizeof($request->ids); $i++) {
                $atividades = OldAtividades::find($request->ids[$i]);
                $atividades->qtd = ($atividades->qtd - $request->qtds[$i]);
                $atividades->save();

                if ($i == 0) $plano_aux = $plano_aux . OldModalidades::find(OldAtividades::find($request->ids[$i])->id_modalidade)->descr;
                else $plano_aux = $plano_aux . ', ' . OldModalidades::find(OldAtividades::find($request->ids[$i])->id_modalidade)->descr;
            }
        }
        else {
            for($i = 0; $i < sizeof($request->ids); $i++) {
                $pedido_planos = PedidoPlanos::find($request->ids[$i]);
                $pedido_planos->qtd_total = ($pedido_planos->qtd_total - $request->qtds[$i]);
                $pedido_planos->valor = $pedido_planos->valor;
                $pedido_planos->save();

                if ($i == 0) $plano_aux = $plano_aux . TabelaPrecos::find(PedidoPlanos::find($request->ids[$i])->id_plano)->descr;
                else $plano_aux = $plano_aux . ', ' . TabelaPrecos::find(PedidoPlanos::find($request->ids[$i])->id_plano)->descr;
            }
        }

        $mov_credito = new MovCredito;
        $mov_credito->id_pedido = $request->id_contrato;
        $mov_credito->id_pessoa = $request->id_pessoa;
        $mov_credito->valor = $request->valor_total;
        $mov_credito->tipo_transacao = 'E';
        $mov_credito->planos = $plano_aux;
        $mov_credito->saldo = $pessoa->creditos;
        $mov_credito->created_by = Auth::user()->name;
        $mov_credito->save();

        return "true";
    }
    public function listar_mov_credito($id, $dinicial, $dfinal) {
        $movimentacoes =  DB::table('mov_credito')
                            ->select('mov_credito.id',
                                        'mov_credito.id_pedido',
                                        'mov_credito.planos',
                                        'mov_credito.created_at',
                                        'mov_credito.valor',
                                        'mov_credito.created_by',
                                        'mov_credito.tipo_transacao')
                            ->where('mov_credito.id_pessoa', $id)
                            ->orderBy('created_at', 'DESC')
                            ->get();
        $creditos = Pessoa::find($id)->creditos;

        $data = new \StdClass;
        $data->movimentacoes = $movimentacoes;
        $data->creditos = $creditos;
        return json_encode($data);
    }
    public function mostrar_creditos_restantes($id) {
        return Pessoa::find($id)->creditos;
    }

    public function filtrar_pesquisa(Request $request) {
        $retorno = $this->filtrar_pesquisaAux("", $request);
        if (sizeof($retorno) < 1) $retorno = $this->filtrar_pesquisaAux("%", $request);
        return json_encode($retorno);
    }
    private function filtrar_pesquisaAux($inicio, Request $request){
        $sql = array("(", "(");
        $fltr = explode(" ", $request->filtro);
        for ($i = 0; $i < count($sql); $i++) {
            for ($j = 0; $j < count($fltr); $j++) {
                if ($sql[$i] != "(") $sql[$i] .= " and ";
                $sql[$i] .= "paciente.";
                $sql[$i] .= $i == 0 ? "nome_fantasia" : "nome_reduzido";
                $sql[$i] .= " like '".$inicio.$fltr[$j]."%'";
            }
            $sql[$i] .= ")";
        }
        return
            DB::select(DB::raw(
                "SELECT old_contratos.id, old_contratos.id as num_pedido, 
                        old_contratos.situacao       as status,
                        old_contratos.pessoas_id  as id_paciente,
                        paciente.nome_fantasia    as descr_paciente,
                        old_financeira.descr      as descr_convenio,
                        old_contratos.responsavel as descr_prof_exa,
                        old_contratos.responsavel as created_by,
                        CONCAT(old_contratos.datainicial, ' ', old_contratos.horainicial) AS created_at,
                        CONCAT(old_contratos.datafinal, ' ', old_contratos.horafinal) AS data_validade,
                        old_contratos.valor_contrato as total,
                        1 as sistema_antigo
                FROM `old_contratos`
                    INNER JOIN pessoa as paciente on old_contratos.pessoas_id = paciente.id
                    INNER JOIN old_finanreceber on old_contratos.id = old_finanreceber.id_contrato
                    INNER JOIN old_financeira on old_finanreceber.id_financeira = old_financeira.id
                WHERE
                    old_contratos.id_emp = ". getEmpresa() ." AND
                    (".$sql[0]." OR ".$sql[1]." )
                GROUP BY 
                    old_contratos.id, 
                    old_contratos.situacao, 
                    old_contratos.situacao, 
                    old_contratos.pessoas_id, 
                    paciente.nome_fantasia, 
                    old_financeira.descr, 
                    old_contratos.responsavel, 
                    old_contratos.datainicial, 
                    old_contratos.horainicial, 
                    old_contratos.datafinal, 
                    old_contratos.horafinal, 
                    old_contratos.valor_contrato

                UNION ALL
                
                SELECT 
                    pedido.id, 
                    pedido.id as num_pedido, 
                    pedido.status, 
                    pedido.id_paciente, 
                    paciente.nome_fantasia as descr_paciente, 
                    convenio.descr as descr_convenio, 
                    CASE WHEN pedido.obs = 'sistema antigo' THEN pedido.created_by ELSE prof_exa.nome_fantasia END AS descr_prof_exa, 
                    users.name as created_by, 
                    pedido.created_at, 
                    pedido.data_validade, 
                    pedido.total,
                    0 as sistema_antigo
                FROM 
                `pedido`
                    left join `pessoa` as `paciente` on `paciente`.`id` = `pedido`.`id_paciente` 
                    left join `pessoa` as `prof_exa` on `prof_exa`.`id` = `pedido`.`id_prof_exa` 
                    left join `convenio` on `convenio`.`id` = `pedido`.`id_convenio` 
                    left join `users` on `users`.`id` = `pedido`.`created_by` 
                WHERE 
                pedido.id_emp = ". getEmpresa() ." AND (
                    ".$sql[0]." OR ".$sql[1]." 
                ) AND (
                    `pedido`.`lixeira` = 0 
                    OR `pedido`.`lixeira` IS NULL
                ) GROUP BY
                pedido.id, 
                pedido.status, 
                pedido.id_paciente, 
                paciente.nome_fantasia,
                convenio.descr,
                pedido.obs,
                pedido.created_by,
                prof_exa.nome_fantasia,
                descr_prof_exa,
                users.name, 
                pedido.created_at, 
                pedido.data_validade, 
                pedido.total LIMIT 50"
            ));
    }

    use AuthenticatesUsers;
    public function validarSupervisor(Request $request) {
        $query = DB::select(DB::raw("
            SELECT
                users.id_profissional,
                users.password
            FROM users
            JOIN supervisor
                ON supervisor.id_profissional = users.id_profissional
            WHERE users.email = '".$request->email."'
                AND supervisor.lixeira = 0
        "));
        if (sizeof($query)) {
            if (Hash::check($request->password, $query[0]->password)) return $query[0]->id_profissional;
            else return 0;
        } else return 0;
    }
}
