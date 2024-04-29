<?php

namespace App\Http\Controllers;

use DB;
use App\AnamnesePessoa;
use Illuminate\Http\Request;

class AnamnesePessoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deletar(Request $request) {
        try {
            $anamnese_pessoa = AnamnesePessoa::find($request->id_anamnese_pessoa);
            $anamnese_pessoa->lixeira = 1;
            $anamnese_pessoa->save();

            // DB::table('anamnese_resposta')
            // ->where('id_anamnese_pessoa', $request->id_anamnese_pessoa)
            // ->delete();

            return $anamnese_pessoa;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    // public function imprimir($id) {
    //     try {

    //         $emp_logo = null;
    //         $path = database_path('empresa') . '/' . 2 . '.png';
    //         if (file_exists($path)) {
    //             $emp_logo = base64_encode(file_get_contents($path));
    //         }

    //         return view('.reports.impresso_anamnese', compact('emp_logo'));
    //     } catch (\Exception $e) {
    //         return $e->getMessage();
    //     }
    // }



    // public function imprimir ($id){
    //     $anamnese = new \stdClass;
    //     $anamnese_pessoa    = AnamnesePessoa::find($id);
    //     $anamneses           = Anamnese::find($anamnese_pessoa->id_anamnese)
    //     $anamnese->perguntas = DB::table("anamnese_pergunta")->where('id_anamnese', $anamnese->id);
    //     foreach ->
    // }



    // public function imprimir($id) {
    //     try {
    //         $pedido_header = DB::table('pedido')
    //                     ->select(
    //                         'pedido.*',
    //                         'paciente.nome_fantasia AS descr_paciente',
    //                         'prof_examinador.nome_fantasia AS descr_prof_exa',
    //                         'convenio.descr AS descr_convenio'
    //                     )
    //                     ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
    //                     ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'pedido.id_prof_exa')
    //                     ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
    //                     ->where('pedido.id', $id)
    //                     ->first();

    //         $pedido_servicos = DB::table('pedido_servicos')
    //                         ->select(
    //                             'pedido_servicos.*',
    //                             'pessoa.nome_fantasia AS descr_prof_exe',
    //                             'procedimento.descr AS descr_procedimento'
    //                         )
    //                         ->leftjoin('pessoa', 'pessoa.id', 'pedido_servicos.id_prof_exe')
    //                         ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
    //                         ->where('pedido_servicos.id_pedido', $id)
    //                         ->get();

    //         $pedido_formas_pag = DB::table('pedido_forma_pag')
    //                         ->select(
    //                             'pedido_forma_pag.*',
    //                             'forma_pag.descr AS descr_forma_pag'
    //                         )
    //                         ->leftjoin('forma_pag', 'forma_pag.id', 'pedido_forma_pag.id_forma_pag')
    //                         ->where('pedido_forma_pag.id_pedido', $id)
    //                         ->get();

    //         foreach($pedido_formas_pag AS $pag) {
    //             $pag->parcelas = DB::table('pedido_parcela')
    //                             ->where('id_pedido_forma_pag', $pag->id)
    //                             ->get();
    //         }

    //         $emp_logo = null;
    //         $path = database_path('empresa') . '/' . getEmpresa() . '.png';
    //         if (file_exists($path)) {
    //             $emp_logo = base64_encode(file_get_contents($path));
    //         }

    //         return view('.reports.impresso_pedido', compact('pedido_header', 'pedido_servicos', 'pedido_formas_pag', 'emp_logo'));
    //     } catch (\Exception $e) {
    //         return $e->getMessage();
    //     }
    // }

    public function listarPorPessoa($id_pessoa) {
        try {
            $anamnese_pessoas = DB::table('anamnese_pessoa')
                        ->select(
                            'anamnese_pessoa.*',
                            'anamnese.descr AS descr_anamnese',
                            'paciente.nome_fantasia AS descr_pessoa',
                            'profissional.nome_fantasia AS descr_membro'
                        )
                        ->leftjoin('anamnese', 'anamnese.id', 'anamnese_pessoa.id_anamnese')
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'anamnese_pessoa.id_pessoa')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'anamnese_pessoa.id_membro')
                        ->where('anamnese_pessoa.id_pessoa', $id_pessoa)
                        ->where('anamnese_pessoa.lixeira', 0)
                        ->orderby('anamnese_pessoa.data', 'DESC')
                        ->orderby('anamnese_pessoa.hora', 'DESC')
                        ->get();

            return json_encode($anamnese_pessoas);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
