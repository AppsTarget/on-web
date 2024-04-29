<?php

namespace App\Http\Controllers;

use DB;
use App\Contrato;
use App\PedidoFormaPag;
use App\PedidoParcela;
use App\PedidoServicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
 
    public function gerar_num() {
        $num_contrato = DB::table('contrato')->max('id');
        if ($num_contrato == '') $num_contrato = 1;
        else                     $num_contrato++;
        return json_encode($num_contrato);
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
        $contratos = DB::table('contrato')
                ->select(
                    "contrato.id",
                    "paciente.nome_fantasia AS descr_paciente",
                    "contrato.Responsavel",
                    "contrato.Valor_contrato",
                    "contrato.Data_inicial",
                    "contrato.Data_final",
                    "contrato.Situacao"
                    // "pedido.status",
                    // "pedido.id_paciente",
                    // "paciente.nome_fantasia AS descr_paciente",
                    // "convenio.descr AS descr_convenio",
                    // DB::raw(
                    //     "CASE" .
                    //     "   WHEN prof_exa.nome_reduzido IS NOT NULL AND prof_exa.nome_reduzido <> '' THEN prof_exa.nome_reduzido" .
                    //     "   ELSE prof_exa.nome_fantasia " .
                    //     "END AS descr_prof_exa"
                    // ),
                    
                    
                    // "users.name AS created_by",
                    // "pedido.created_at",
                    // "pedido.data_validade",
                    // "pedido.total")
                    )
                    ->leftjoin('pessoa AS paciente', 'paciente.id', 'contrato.Pessoa_id')
                // ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
                // ->leftjoin('pessoa AS prof_exa', 'prof_exa.id', 'pedido.id_prof_exa')
                // ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                // ->leftjoin('users', 'users.id', 'pedido.created_by')
                // ->where('pedido.id_emp', getEmpresa())
                // ->orderby('pedido.created_at', 'DESC')
                ->get();

        $convenios = DB::table('convenio')
                    ->where('quem_paga', 'E')
                    ->where('id_emp', getEmpresa())
                    ->get();

        return view('contratos', compact('contratos', ''));

        // return json_encode($contratos);
    }


    public function salvar(Request $request) {
        try {
            $id_tabela_preco = DB::table('convenio')->where('id', $request->id_convenio)->value('id_tabela_preco');
            $num_pedido = DB::table('pedido')->where('id_emp', getEmpresa())->max('num_pedido');
            if ($num_pedido == null) $num_pedido = 1;
            else                     $num_pedido = $num_pedido + 1;

            if ($request->id == 0) {
                $pedido = new pedido;
                $pedido->num_pedido = $num_pedido;
            } else {
                $pedido = pedido::find($request->id);
            }
            $pedido->id_emp = getEmpresa();
            $pedido->id_paciente = $request->id_paciente;
            $pedido->id_convenio = $request->id_convenio;
            $pedido->id_prof_exa = $request->id_profissional_exa;
            $pedido->num_pedido = $num_pedido;
            $pedido->data = date('Y-m-d');
            $pedido->hora = date('H:i:s');
            $pedido->data_validade = date('Y-m-d');
            $pedido->status = $request->status;
            $pedido->obs = $request->obs;
            $pedido->tipo_forma_pag = $request->tipo_forma_pag;
            $pedido->created_by = Auth::user()->id;
            $pedido->save();

            $total = 0;
            DB::table('pedido_servicos')->where('id_pedido', $pedido->id)->delete();
            foreach ($request->procedimentos as $procedimento) {
                $procedimento = (object) $procedimento;

                $pedido_procedimento = new PedidoServicos;
                $pedido_procedimento->id_emp = getEmpresa();
                $pedido_procedimento->id_pedido = $pedido->id;
                $pedido_procedimento->id_tabela_preco = $id_tabela_preco;
                $pedido_procedimento->id_prof_exe = $procedimento->id_exe_profissional;
                $pedido_procedimento->id_procedimento = $procedimento->id_procedimento;
                $pedido_procedimento->dente_regiao = $procedimento->dente_regiao;
                $pedido_procedimento->face = $procedimento->dente_face;
                $pedido_procedimento->qtde = 1;
                $pedido_procedimento->num_guia = '';
                $pedido_procedimento->obs = $procedimento->obs;
                $pedido_procedimento->status = 'A';
                if ($request->tipo_forma_pag == 'V') {
                    $pedido_procedimento->valor = $procedimento->valor;
                    $total += $procedimento->valor;
                } else {
                    $pedido_procedimento->valor = $procedimento->valor_prazo;
                    $total += $procedimento->valor_prazo;
                }
                $pedido_procedimento->save();
            }
            $pedido->total = $total;
            $pedido->save();

            DB::table('pedido_parcela')
            ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id', 'pedido_parcela.id_pedido_forma_pag')
            ->where('pedido_forma_pag.id_pedido', $pedido->id)
            ->delete();
            DB::table('pedido_forma_pag')->where('id_pedido', $pedido->id)->delete();
            foreach ($request->formas_pag as $forma_pag) {
                $forma_pag = (object) $forma_pag;

                $pedido_forma_pag = new PedidoFormaPag;
                $pedido_forma_pag->id_emp = getEmpresa();
                $pedido_forma_pag->id_pedido = $pedido->id;
                $pedido_forma_pag->id_forma_pag = $forma_pag->id_forma_pag;
                $pedido_forma_pag->id_financeira = $forma_pag->id_financeira;
                $pedido_forma_pag->num_total_parcela = $forma_pag->parcela;
                $pedido_forma_pag->valor_total = $forma_pag->forma_pag_valor;
                $pedido_forma_pag->tipo = $request->tipo_forma_pag;
                $pedido_forma_pag->save();

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
                    if ($i == 0) {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento)));
                    } else {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento))) . ' + ' . (($i + 1) * 30) .  ' days'));
                    }
                    $pedido_parcela->save();
                }
            }
            return json_encode($pedido);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mudar_status(Request $request) {
        try {
            $pedido = pedido::find($request->id);
            $pedido->status = $request->status;
            $pedido->save();

            DB::table('pedido_servicos')
            ->where('id_pedido', $request->id)
            ->update([
                'status' => 'C'
            ]);
            return json_encode($pedido);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            Contrato::find($request->id)->delete();
            DB::table('contrato')
            ->where('id', $request->id)
            ->delete();
            return $request->id;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $pedido_header = DB::table('pedido')
                        ->select(
                            'pedido.*',
                            'paciente.nome_fantasia AS descr_paciente',
                            'prof_examinador.nome_fantasia AS descr_prof_exa',
                            'convenio.descr AS descr_convenio'
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
                        ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'pedido.id_prof_exa')
                        ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                        ->where('pedido.id', $id)
                        ->first();

            $pedido_servicos = DB::table('pedido_servicos')
                            ->select(
                                'pedido_servicos.*',
                                'pessoa.nome_fantasia AS descr_prof_exe',
                                'procedimento.descr AS descr_procedimento'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'pedido_servicos.id_prof_exe')
                            ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
                            ->where('pedido_servicos.id_pedido', $id)
                            ->get();

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

            $emp_logo = null;
            $path = database_path('empresa') . '/' . getEmpresa() . '.png';
            if (file_exists($path)) {
                $emp_logo = base64_encode(file_get_contents($path));
            }

            return view('.reports.impresso_pedido', compact('pedido_header', 'pedido_servicos', 'pedido_formas_pag', 'emp_logo'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa) {
        $contratos = DB::table('contrato')
            ->select(
                "contrato.id",
                "paciente.nome_fantasia AS descr_paciente",
                "contrato.Responsavel",
                "contrato.Valor_contrato",
                "contrato.Data_inicial",
                "contrato.Data_final",
                "contrato.Situacao"
                )
                ->leftjoin('pessoa AS paciente', 'paciente.id', 'contrato.Pessoa_id')
                ->where("paciente.id", $id_pessoa)
            ->get();

    $convenios = DB::table('convenio')
                ->where('quem_paga', 'E')
                ->where('id_emp', getEmpresa())
                ->get();

        return json_encode($contratos);
    }
}