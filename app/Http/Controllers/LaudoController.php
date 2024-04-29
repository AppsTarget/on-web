<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use App\Laudo;

class LaudoController extends Controller {
    public function createLaudo(Request $request) {
        $data = new Laudo;
        $data->id_pessoa = $request->id_pessoa;
        $data->id_prof = Auth::user()->id_profissional;
        $data->grafico = $request->grafico;
        $data->diagnostico = $request->diagnostico;
        $data->save();
        return redirect($request->endereco);
    }

    public function imprimir($id_laudo) {
        
        $laudo = DB::table('laudo')
                 ->select(DB::raw("
                    laudo.grafico,
                    laudo.id,
                    pessoa.id AS id_paciente,
                    laudo.diagnostico,
                    laudo.grafico,
                    pessoa.nome_fantasia AS paciente,
                    profissional.nome_fantasia AS profissional,
                    laudo.created_at,
                    DATE_FORMAT(laudo.created_at, '%d/%m%/%Y') AS dtCriacao
                 "))
                 ->leftjoin('pessoa', 'pessoa.id', 'laudo.id_pessoa')
                 ->leftjoin('pessoa AS profissional', 'profissional.id', 'laudo.id_prof')
                 ->where('laudo.id', $id_laudo)
                 ->first();
        return view('reports.impresso_laudo', compact('laudo'));
    }

    public function getDataGrafico($id_laudo) {
        return DB::table('laudo')->where('id', $id_laudo)->value('grafico');
    }

    public function deletar(Request $request) {
        $data = Laudo::find($request->id);
        $data->dump = 1;
        $data->save();

        return 'true';
    }
}