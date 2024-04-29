<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\EvolucaoPedido;
use Illuminate\Http\Request;

class EvolucaoPedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $evolucao = new EvolucaoPedido;
            $evolucao->id_emp = getEmpresa();
            $evolucao->id_pedido_servicos = $request->id_pedido_servicos;
            $evolucao->id_evolucao_tipo = $request->id_evolucao_tipo;
            $evolucao->data = implode('-', array_reverse(explode('/', $request->data)));
            $evolucao->hora = $request->hora;

            if (getEmpresaObj()->tipo == 'M') {
                $evolucao->titulo = $request->titulo;
                $evolucao->cid = $request->cid;
                $evolucao->estado = $request->estado;
            }

            $evolucao->diagnostico = $request->diagnostico;
            $evolucao->id_profissional = Auth::user()->id_profissional;
            $evolucao->id_paciente = $request->id_paciente;
            $evolucao->save();

            return json_encode($evolucao);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $evolucao = EvolucaoPedido::find($request->id);
            $evolucao->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar($id_pedido_servicos) {
        try {
            $evolucoes = DB::table('evolucao_pedido')
                        ->select(
                            'evolucao_pedido.*',
                            'evolucao_tipo.descr AS descr_evolucao_tipo',
                            'pessoa.nome_fantasia AS descr_profissional'
                        )
                        ->leftjoin('pessoa', 'pessoa.id', 'evolucao_pedido.id_profissional')
                        ->leftjoin('evolucao_tipo', 'evolucao_tipo.id', 'evolucao_pedido.id_evolucao_tipo')
                        ->where('id_pedido_servicos', $id_pedido_servicos)
                        ->orderby('evolucao_pedido.data', 'DESC')
                        ->orderby('evolucao_pedido.hora', 'DESC')
                        ->get();

            return json_encode($evolucoes);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
