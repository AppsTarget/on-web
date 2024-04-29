<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Prescricao;
use Illuminate\Http\Request;

class PrescricaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $prescricao = new Prescricao;
            $prescricao->id_emp = getEmpresa();
            $prescricao->id_paciente = $request->id_paciente;
            $prescricao->id_profissional = Auth::user()->id_profissional;
            $prescricao->data = implode('-', array_reverse(explode('/', $request->data)));
            $prescricao->corpo = $request->corpo;
            $prescricao->save();

            return json_encode($prescricao);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $prescricao = Prescricao::find($request->id);
            $prescricao->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $prescricao = DB::table('prescricao')
                        ->select(
                            'prescricao.id',
                            'prescricao.data',
                            'pessoa.nome_fantasia AS paciente_nome',
                            'prescricao.corpo'
                        )
                        ->join('pessoa', 'pessoa.id', 'prescricao.id_paciente')
                        ->where('prescricao.id', $id)
                        ->first();

            return view('reports.impresso-prescricao', compact('prescricao'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function listarPorPessoa($id_pessoa) {
        try {
            $prescricoes = DB::table('prescricao')
                        ->select(
                            'prescricao.*',
                            'paciente.nome_fantasia AS descr_paciente',
                            'profissional.nome_fantasia AS descr_profissional'
                        )
                        ->leftjoin('pessoa AS paciente',     'paciente.id',     'prescricao.id_paciente')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'prescricao.id_profissional')
                        ->where('id_paciente', $id_pessoa)
                        ->orderby('prescricao.data', 'DESC')
                        ->get();

            return json_encode($prescricoes);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
