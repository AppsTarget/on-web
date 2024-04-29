<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\PedidoServicos;
use Illuminate\Http\Request;

class PedidoServicosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function mostrar($id_pedido_servicos) {
        try {
            return json_encode(
                DB::table('pedido_servicos')
                ->select(
                    'pedido_servicos.*',
                    'procedimento.descr AS descr_procedimento',
                    'pessoa.nome_fantasia AS descr_prof_exe'
                )
                ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
                ->leftjoin('pessoa', 'pessoa.id', 'pedido_servicos.id_prof_exe')
                ->where('pedido_servicos.id', $id_pedido_servicos)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa) {
        $procedimentos =
            DB::table('pedido_servicos')
            ->select(
                'pedido_servicos.*',
                DB::raw(
                    "CASE" .
                    "   WHEN pessoa.nome_reduzido IS NOT NULL AND pessoa.nome_reduzido <> '' THEN pessoa.nome_reduzido" .
                    "   ELSE pessoa.nome_fantasia " .
                    "END AS descr_prof_exe"
                ),
                DB::raw(
                    "CASE" .
                    "   WHEN pessoa_final.nome_reduzido IS NOT NULL AND pessoa_final.nome_reduzido <> '' THEN pessoa_final.nome_reduzido" .
                    "   ELSE pessoa_final.nome_fantasia " .
                    "END AS descr_prof_finalizado"
                ),
                'procedimento.descr AS descr_procedimento',
                'preco.valor AS valor_vista',
                'preco.valor_prazo',
                DB::raw(
                    '(SELECT COUNT(*)' .
                    '   FROM evolucao_pedido' .
                    '  WHERE evolucao_pedido.id_pedido_servicos = pedido_servicos.id) AS qtde_evolucao'
                ),
                "pedido_servicos.valor",
                "convenio.descr as descr_convenio"
            )
            ->leftjoin('pessoa', 'pessoa.id', 'pedido_servicos.id_prof_exe')
            ->leftjoin('pessoa AS pessoa_final', 'pessoa_final.id', 'pedido_servicos.id_prof_finalizado')
            ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
            ->leftjoin('pedido', 'pedido.id', 'pedido_servicos.id_pedido')
            ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
            ->leftjoin('preco', function($join) {
                $join->on('preco.id_tabela_preco', 'pedido_servicos.id_tabela_preco');
                $join->on('preco.id_procedimento', 'pedido_servicos.id_procedimento');
            })
            ->where('pedido.id_paciente', $id_pessoa)
            ->orderby(DB::raw(
                " CASE" .
                "   WHEN pedido_servicos.status = 'C' THEN 4" .
                "   WHEN pedido_servicos.status = 'F' THEN 3" .
                "   WHEN (SELECT COUNT(*)" .
                "           FROM evolucao_pedido" .
                "          WHERE evolucao_pedido.id_pedido_servicos = pedido_servicos.id) > 0 THEN 1" .
                "   ELSE 2" .
                " END"
            ))
            ->orderby('data', 'DESC')
            ->orderby('hora', 'DESC')
            ->get();

        return json_encode($procedimentos);
    }

    public function finalizar(Request $request) {
        try {
            $pedido_servicos = PedidoServicos::find($request->id_pedido_servicos);
            $pedido_servicos->status = 'F';
            $pedido_servicos->id_prof_finalizado = $request->id_profissional;
            $pedido_servicos->data_finalizado = date('Y-m-d', strtotime(str_replace("/", "-", $request->data)));
            $pedido_servicos->hora_finalizado = $request->hora;
            $pedido_servicos->save();

            return json_encode($pedido_servicos);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function cancelar(Request $request) {
        try {
            $pedido_servicos = PedidoServicos::find($request->id);
            $pedido_servicos->status = 'C';
            $pedido_servicos->save();

            return json_encode($pedido_servicos);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
