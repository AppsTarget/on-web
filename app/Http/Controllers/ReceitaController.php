<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Receita;
use App\ReceitaMedicamento;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $receita = new Receita;
            $receita->id_emp = getEmpresa();
            $receita->id_paciente = $request->id_paciente;
            $receita->id_profissional = Auth::user()->id_profissional;
            $receita->save();

            foreach ($request->medicamentos as $index => $medicamento) {
                $receita_medicamento = new ReceitaMedicamento;
                $receita_medicamento->id_emp = getEmpresa();
                $receita_medicamento->id_receita = $receita->id;
                $receita_medicamento->id_medicamento = $medicamento['id'];
                $receita_medicamento->descr_medicamento = $medicamento['descr'];
                $receita_medicamento->posologia = $medicamento['posologia'];
                $receita_medicamento->save();
            }
            return $request->medicamentos;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar_receita_medicamentos($id_receita) {
        try {
            return DB::table('receita_medicamento')
                    ->select(
                        'receita_medicamento.id_receita',
                        'receita_medicamento.id_medicamento',
                        DB::raw(
                            'CASE ' .
                            '    WHEN medicamento.id IS NULL THEN receita_medicamento.descr_medicamento ' .
                            '    ELSE                             medicamento.descr ' .
                            'END AS descr_medicamento '
                        ),
                        'receita_medicamento.posologia'
                    )
                    ->leftjoin('medicamento', 'medicamento.id', 'receita_medicamento.id_medicamento')
                    ->where('receita_medicamento.id_receita', $id_receita)
                    ->get();

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $receita = DB::table('receita')
                    ->where('id', $id)
                    ->first();

            $receita_medicamentos = DB::table('receita_medicamento')
                ->select(
                    'receita_medicamento.*',
                    DB::raw(
                        'CASE ' .
                        '    WHEN medicamento.id IS NULL THEN receita_medicamento.descr_medicamento ' .
                        '    ELSE                             medicamento.descr ' .
                        'END AS descr_medicamento '
                    )
                )
                ->leftjoin('medicamento', 'medicamento.id', 'receita_medicamento.id_medicamento')
                ->where('receita_medicamento.id_receita', $id)
                ->get();

            $pessoa = DB::table('pessoa')
                        ->where('id', $receita->id_paciente)
                        ->first();

            if (getEmpresaObj()->mod_impressao_especifica) {
                return view(
                    '.reports.' . getEmpresa() . '.impresso_receita',
                    compact('receita', 'receita_medicamentos', 'pessoa')
                );
            } else {
                return view(
                    '.reports.impresso_receita',
                    compact('receita', 'receita_medicamentos', 'pessoa')
                );
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $receita = Receita::find($request->id);
            $receita->delete();

            DB::table('receita_medicacmento')
            ->where('id_receita', $request->id)
            ->delete();

            return $receita;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa) {
        try {
            $receitas = DB::table('receita')
                    ->select(
                        'receita.id',
                        DB::raw('GROUP_CONCAT(DISTINCT rm2.descr_medicamento) AS descr'),
                        'pessoa.nome_fantasia AS nome_profissional',
                        'receita.created_at',
                        'receita_medicamento.id AS id_receita_medicamento',
                        'receita_medicamento.descr_medicamento',
                        'receita_medicamento.posologia'
                    )
                    ->leftjoin('receita_medicamento', 'receita_medicamento.id_receita', 'receita.id')
                    ->leftjoin('receita_medicamento AS rm2', 'rm2.id_receita', 'receita.id')
                    ->leftjoin('pessoa', 'pessoa.id', 'receita.id_profissional')
                    ->where('receita.id_paciente', $id_pessoa)
                    ->groupby(
                        'receita.id',
                        'pessoa.nome_fantasia',
                        'receita.created_at',
                        'receita_medicamento.id',
                        'receita_medicamento.descr_medicamento',
                        'receita_medicamento.posologia'
                    )
                    ->orderby('receita.created_at', 'DESC')
                    ->get();

            return json_encode($receitas);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
