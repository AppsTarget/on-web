<?php

namespace App\Http\Controllers;

use DB;
use App\Orcamento;
use App\OrcamentoFormaPag;
use App\OrcamentoServicos;
use App\Pedido;
use App\PedidoFormaPag;
use App\PedidoParcela;
use App\PedidoServicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrcamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gerar_num() {
        $num_pedido = DB::table('orcamento')->max('num_pedido');
        if ($num_pedido == '') $num_pedido = 1;
        else                   $num_pedido++;
        return $num_pedido;
    }

    public function mostrar($id_orcamento) {
        try {
            $data = new \stdClass;
            $data->orcamento = DB::table('orcamento')
                            ->select(
                                'orcamento.*',
                                'paciente.nome_fantasia AS descr_paciente',
                                'prof_examinador.nome_fantasia AS descr_prof_exa',
                                'convenio.descr AS descr_convenio'
                            )
                            ->leftjoin('pessoa AS paciente', 'paciente.id', 'orcamento.id_paciente')
                            ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'orcamento.id_prof_exa')
                            ->leftjoin('convenio', 'convenio.id', 'orcamento.id_convenio')
                            ->where('orcamento.id', $id_orcamento)
                            ->first();

            $data->convenio_paciente = DB::table('convenio_pessoa')
                        ->select(
                            'convenio.id',
                            'convenio.descr'
                        )
                        ->leftjoin('convenio', 'convenio.id', 'convenio_pessoa.id_convenio')
                        ->where('convenio.id_emp', getEmpresa())
                        ->where(function($sql) use($data) {
                            $sql->where('convenio_pessoa.id_paciente', $data->orcamento->id_paciente)
                                ->orWhere('convenio.quem_paga', 'E');
                        })
                        ->groupby(
                            'convenio.id',
                            'convenio.descr'
                        )
                        ->get();

            $data->orc_procedimentos = DB::table('orcamento_servicos')
                            ->select(
                                'orcamento_servicos.*',
                                DB::raw(
                                    "CASE" .
                                    "   WHEN pessoa.nome_reduzido IS NOT NULL AND pessoa.nome_reduzido <> '' THEN pessoa.nome_reduzido" .
                                    "   ELSE pessoa.nome_fantasia " .
                                    "END AS descr_prof_exe"
                                ),
                                'procedimento.descr AS descr_procedimento'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'orcamento_servicos.id_prof_exe')
                            ->leftjoin('procedimento', 'procedimento.id', 'orcamento_servicos.id_procedimento')
                            ->where('orcamento_servicos.id_orcamento', $id_orcamento)
                            ->get();

            $data->orc_formas_pag = DB::table('orcamento_forma_pag')
                                ->select(
                                    'orcamento_forma_pag.*',
                                    'forma_pag.descr AS descr_forma_pag'
                                )
                                ->leftjoin('forma_pag', 'forma_pag.id', 'orcamento_forma_pag.id_forma_pag')
                                ->where('orcamento_forma_pag.id_orcamento', $id_orcamento)
                                ->get();

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        $orcamentos = DB::table('orcamento')
                ->select(
                    "orcamento.id",
                    "orcamento.num_pedido",
                    "orcamento.status",
                    "orcamento.id_paciente",
                    "paciente.nome_fantasia AS descr_paciente",
                    "convenio.descr AS descr_convenio",
                    DB::raw(
                        "CASE" .
                        "   WHEN prof_exa.nome_reduzido IS NOT NULL AND prof_exa.nome_reduzido <> '' THEN prof_exa.nome_reduzido" .
                        "   ELSE prof_exa.nome_fantasia " .
                        "END AS descr_prof_exa"
                    ),
                    "users.name AS created_by",
                    "orcamento.created_at",
                    "orcamento.data_validade",
                    DB::raw(
                        "(SELECT COUNT(*) " .
                        "   FROM orcamento_servicos " .
                        "  WHERE orcamento_servicos.id_orcamento = orcamento.id" .
                        "    AND orcamento_servicos.autorizado = 'S')" .
                        " AS qtde_servicos_autorizados"
                    ),
                    "orcamento.total",
                    "orcamento.total_prazo"
                )
                ->leftjoin('pessoa AS paciente', 'paciente.id', 'orcamento.id_paciente')
                ->leftjoin('pessoa AS prof_exa', 'prof_exa.id', 'orcamento.id_prof_exa')
                ->leftjoin('convenio', 'convenio.id', 'orcamento.id_convenio')
                ->leftjoin('users', 'users.id', 'orcamento.created_by')
                ->where('orcamento.id_emp', getEmpresa())
                ->orderby('orcamento.created_at', 'DESC')
                ->get();

        $convenios = DB::table('convenio')
                    ->where('quem_paga', 'E')
                    ->where('id_emp', getEmpresa())
                    ->get();

        return view('orcamento', compact('orcamentos', 'convenios'));
    }

    function salvar(Request $request) {
        try {
            $id_tabela_preco = DB::table('convenio')->where('id', $request->id_convenio)->value('id_tabela_preco');
            $num_pedido = DB::table('orcamento')->where('id_emp', getEmpresa())->max('num_pedido');

            if ($num_pedido == null) $num_pedido = 1;
            else                     $num_pedido = $num_pedido + 1;

            if ($request->id == 0) {
                $orcamento = new Orcamento;
                $orcamento->num_pedido = $num_pedido;
            } else {
                $orcamento = Orcamento::find($request->id);
            }
            $orcamento->id_emp = getEmpresa();
            $orcamento->id_paciente = $request->id_paciente;
            $orcamento->id_convenio = $request->id_convenio;
            $orcamento->id_prof_exa = $request->id_profissional_exa;
            $orcamento->num_pedido = $num_pedido;
            $orcamento->data = date('Y-m-d');
            $orcamento->hora = date('H:i:s');
            $orcamento->data_validade = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_validade)));
            $orcamento->status = $request->status;
            $orcamento->obs = $request->obs;
            $orcamento->total = 0;
            $orcamento->total_prazo = 0;
            $orcamento->total_aprovado = 0;
            $orcamento->created_by = Auth::user()->id;
            $orcamento->save();

            $total = 0;
            $total_prazo = 0;
            DB::table('orcamento_servicos')->where('id_orcamento', $orcamento->id)->delete();
            foreach($request->procedimentos as $procedimento) {
                $procedimento = (object) $procedimento;
                $os = new OrcamentoServicos;
                $os->id_emp = getEmpresa();
                $os->id_orcamento = $orcamento->id;
                $os->id_tabela_preco = $id_tabela_preco;
                $os->id_procedimento = $procedimento->procedimento_id;
                $os->id_prof_exe = $procedimento->profissional_exe_id;
                $os->dente_regiao = $procedimento->dente_regiao;
                $os->face = $procedimento->dente_face;
                $os->qtde = 1;
                $os->valor = $procedimento->valor;
                $os->valor_prazo = $procedimento->valor_prazo;
                $os->autorizado = 'N';
                $os->obs = $procedimento->obs;
                $os->save();

                $total = $total + $procedimento->valor;
                $total_prazo = $total_prazo + $procedimento->valor_prazo;
            }
            $orcamento->total = $total;
            $orcamento->total_prazo = $total_prazo;
            $orcamento->save();

            DB::table('orcamento_forma_pag')->where('id_orcamento', $orcamento->id)->delete();
            foreach($request->formas_pag as $forma_pag) {
                $forma_pag = (object) $forma_pag;
                $orc_forma_pag = new OrcamentoFormaPag;
                $orc_forma_pag->id_emp = getEmpresa();
                $orc_forma_pag->id_orcamento = $orcamento->id;
                $orc_forma_pag->id_forma_pag = $forma_pag->forma_pag;
                // $orc_forma_pag->id_financeira = $forma_pag->financeira_id;
                $orc_forma_pag->num_parcela = $forma_pag->parcela;
                $orc_forma_pag->valor = $forma_pag->valor;
                $orc_forma_pag->tipo = $forma_pag->tipo;
                $orc_forma_pag->save();
            }
            return json_encode($orcamento);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mudar_status(Request $request) {
        try {
            $orcamento = Orcamento::find($request->id);
            $orcamento->status = $request->status;
            $orcamento->save();
            return json_encode($orcamento);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            Orcamento::find($request->id)->delete();
            DB::table('orcamentos_servicos')
            ->where('id', $request->id)
            ->delete();
            return $request->id;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $orcamento_header = DB::table('orcamento')
                        ->select(
                            'orcamento.*',
                            'paciente.nome_fantasia AS descr_paciente',
                            'prof_examinador.nome_fantasia AS descr_prof_exa',
                            'convenio.descr AS descr_convenio'
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'orcamento.id_paciente')
                        ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'orcamento.id_prof_exa')
                        ->leftjoin('convenio', 'convenio.id', 'orcamento.id_convenio')
                        ->where('orcamento.id', $id)
                        ->first();

            $orcamento_servicos = DB::table('orcamento_servicos')
                            ->select(
                                'orcamento_servicos.*',
                                'pessoa.nome_fantasia AS descr_prof_exe',
                                'procedimento.descr AS descr_procedimento'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'orcamento_servicos.id_prof_exe')
                            ->leftjoin('procedimento', 'procedimento.id', 'orcamento_servicos.id_procedimento')
                            ->where('orcamento_servicos.id_orcamento', $id)
                            ->get();

            $orcamento_formas_pag = DB::table('orcamento_forma_pag')
                            ->select(
                                'orcamento_forma_pag.*',
                                'forma_pag.descr AS descr_forma_pag'
                            )
                            ->leftjoin('forma_pag', 'forma_pag.id', 'orcamento_forma_pag.id_forma_pag')                            ->where('orcamento_forma_pag.id_orcamento', $id)
                            ->get();

            $emp_logo = null;
            $path = database_path('empresa') . '/' . getEmpresa() . '.png';
            if (file_exists($path)) {
                $emp_logo = base64_encode(file_get_contents($path));
            }

            return view('.reports.impresso_orcamento', compact('orcamento_header', 'orcamento_servicos', 'orcamento_formas_pag', 'emp_logo'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function conversao_plano(Request $request) {
        try {
            $num_pedido = DB::table('pedido')->max('num_pedido');
            if ($num_pedido == '') $num_pedido = 1;
            else                   $num_pedido++;

            $pedido = new Pedido;
            $pedido->id_emp = getEmpresa();
            $pedido->id_orcamento = $request->id_orcamento;
            $pedido->id_paciente = $request->id_paciente;
            $pedido->id_convenio = $request->id_convenio;
            $pedido->id_prof_exa = $request->id_profissional_exa;
            $pedido->num_pedido = $num_pedido;
            $pedido->data = date('Y-m-d');
            $pedido->hora = date('H:i:s');
            $pedido->data_validade = date('Y-m-d');
            $pedido->status = 'F';
            $pedido->obs = $request->obs;
            $pedido->tipo_forma_pag = $request->tipo_forma_pag;
            $pedido->created_by = Auth::user()->id;
            $pedido->save();

            $total = 0;
            foreach ($request->procedimentos as $procedimento) {
                $procedimento = (object) $procedimento;

                $pedido_procedimento = new PedidoServicos;
                $pedido_procedimento->id_emp = getEmpresa();
                $pedido_procedimento->id_pedido = $pedido->id;
                $pedido_procedimento->id_orcamento_servico = $procedimento->id_orcamento_servico;
                $pedido_procedimento->id_tabela_preco = DB::table('orcamento_servicos')->where('id', $procedimento->id_orcamento_servico)->value('id_tabela_preco');
                $pedido_procedimento->id_prof_exe = $procedimento->id_exe_profissional;
                $pedido_procedimento->id_procedimento = $procedimento->id_procedimento;
                $pedido_procedimento->dente_regiao = $procedimento->dente_regiao;
                $pedido_procedimento->face = $procedimento->dente_face;
                $pedido_procedimento->qtde = 1;
                $pedido_procedimento->valor = $procedimento->valor;
                $pedido_procedimento->num_guia = '';
                $pedido_procedimento->obs = $procedimento->procedimento_obs;
                $pedido_procedimento->save();

                $orcamento_servico = OrcamentoServicos::find($procedimento->id_orcamento_servico);
                $orcamento_servico->autorizado = 'S';
                $orcamento_servico->save();

                $total += $procedimento->valor;
            }
            $pedido->total = $total;
            $pedido->save();

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

                for ($i = 0; $i <= $request->parcela; $i++) {
                    $pedido_parcela = new PedidoParcela;
                    $pedido_parcela->id_emp = getEmpresa();
                    $pedido_parcela->id_pedido_forma_pag = $pedido_forma_pag->id;
                    $pedido_parcela->parcela = $i;
                    if ($acrescimo > 0 && $i == 0) $pedido_parcela->valor = $valor_parcela + $acrescimo;
                    else                           $pedido_parcela->valor = $valor_parcela;
                    if ($i == 1) {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_vencimento)));
                    } else {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $request->data_vencimento))) . ' + ' . ($i * 30) .  ' days'));
                    }
                    $pedido_parcela->save();
                }
            }

            $orcamento = Orcamento::find($request->id_orcamento);
            if (DB::table('orcamento_servicos')
                ->where('id_orcamento', $request->id_orcamento)
                ->where('autorizado', 'S')
                ->count() ==
                DB::table('orcamento_servicos')
                ->where('id_orcamento', $request->id_orcamento)
                ->count()) {
                $orcamento->status = 'F';
            } else {
                $orcamento->status = 'P';
            }
            $orcamento->save();

            return json_encode($pedido);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa) {
        $orcamentos = DB::table('orcamento')
                ->select(
                    "orcamento.id",
                    "orcamento.num_pedido",
                    "orcamento.status",
                    "paciente.nome_fantasia AS descr_paciente",
                    DB::raw(
                        "CASE" .
                        "   WHEN prof_exa.nome_reduzido IS NOT NULL AND prof_exa.nome_reduzido <> '' THEN prof_exa.nome_reduzido" .
                        "   ELSE prof_exa.nome_fantasia " .
                        "END AS descr_prof_exa"
                    ),
                    "users.name AS created_by",
                    "orcamento.created_at",
                    "orcamento.data_validade",
                    "orcamento.total AS valor",
                    "orcamento.total_prazo AS aprazo",
                    "convenio.descr as descr_convenio"
                )
                ->leftjoin('pessoa AS paciente', 'paciente.id', 'orcamento.id_paciente')
                ->leftjoin('pessoa AS prof_exa', 'prof_exa.id', 'orcamento.id_prof_exa')
                ->leftjoin('users', 'users.id', 'orcamento.created_by')
                ->leftjoin('convenio', 'convenio.id', 'orcamento.id_convenio')
                ->where('orcamento.id_paciente', $id_pessoa)
                ->where('orcamento.id_emp', getEmpresa())
                ->orderby('orcamento.created_at', 'DESC')
                ->get();

        return json_encode($orcamentos);
    }
}
