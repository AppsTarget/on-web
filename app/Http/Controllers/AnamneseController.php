<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Anamnese;
use App\AnamnesePergunta;
use App\AnamneseOpcao;
use App\AnamnesePessoa;
use App\AnamneseResposta;
use Illuminate\Http\Request;

class AnamneseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function criar() {
        $especialidades = DB::table('especialidade')->where('lixeira', '<>', 1)->get();
        return view('criar_anamnese', compact('especialidades'));
    }

    public function editar($id) {
        $especialidades = DB::table('especialidade')->where('lixeira', '<>', 1)->get();
        return view('editar_anamnese', compact('especialidades', 'id'));
    }

    public function salvar(Request $request) {
        try {
            if($request->id != 0){
                $perguntas = DB::table('anamnese_pergunta')->where('id_anamnese', $request->id)->delete();
                $anamnese = Anamnese::find($request->id);
            }
            else $anamnese = new Anamnese;

            // $anamnese->id_emp = getEmpresa();
            $anamnese->descr = $request->descr;
            $anamnese->ativo = true;
            $anamnese->save();

            foreach ($request->perguntas as $pergunta) {
                $ap = new AnamnesePergunta;
                // $ap->id_emp = getEmpresa();
                $ap->id_anamnese = $anamnese->id;
                $ap->tipo = $pergunta['tipo'];
                $ap->pergunta = trim($pergunta['descr']);
                $ap->obs = trim($pergunta['obs']);
                $ap->save();

                if ($pergunta['tipo'] == 'C') {
                    foreach ($pergunta['opcoes'] as $opcao) {
                        $op = new AnamneseOpcao;
                        // $op->id_emp = getEmpresa();
                        $op->id_pergunta = $ap->id;
                        $op->descr = $opcao;
                        $op->save();
                    }
                }
            }
            return $anamnese;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $anamneses = DB::table('anamnese')
                        // ->where('id_emp', getEmpresa())
                        ->get();

            return view('anamnese', compact('anamneses'));
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function mostrar_anamnese($id) {
        try {
            $anamnese = new \stdClass;
            $anamnese->id = $id;
            $anamnese->descr = DB::table('anamnese')
                        // ->where('id_emp', getEmpresa())
                        ->where('id', $id)
                        ->value('descr');

            $anamnese->perguntas = DB::table('anamnese_pergunta')
                                    ->where('id_anamnese', $id)
                                    ->get();

            foreach ($anamnese->perguntas as $pergunta) {
                if ($pergunta->tipo == 'C') {
                    $pergunta->opcoes = DB::table('anamnese_opcao')
                                        ->where('id_pergunta', $pergunta->id)
                                        ->get();
                }
            }

            return json_encode($anamnese);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function exibir_para_editar($id){
        $data = new \stdClass;
        $data->descricao = DB::table('anamnese')
                                  ->where('id', $id)
                                  ->value('descr');
        $data->anamnese_questao = DB::table('anamnese_pergunta')
                       ->where('id_anamnese', $id)
                       ->orderBy('id')
                       ->get();
        $opcoes = array();
        for ($i = 0; $i < sizeof($data->anamnese_questao); $i++) {
            if ($data->anamnese_questao[$i]->tipo == "C") {
                $nome = "id_".$data->anamnese_questao[$i]->id;
                array_push($opcoes, $nome);
                $opcoes[$nome] = DB::table('anamnese_opcao')
                                     ->where('id_pergunta', $data->anamnese_questao[$i]->id)
                                     ->get();
            }
        }
        
        return compact("data", "opcoes");
    }

    public function listar_opcoes($id){
        $opcoes = DB::table('anamnese_opcao')
                  ->where('id_pergunta', $id)
                  ->get();
        return $opcoes;
    }

    // function mostrar_resposta($id) {
    //     try {
    //         $anamnese = new \stdClass;
    //         $anamnese_pessoa = AnamnesePessoa::find($id);
    //         $anamnese->id = $anamnese_pessoa->id;
    //         $anamnese->descr = DB::table('anamnese')
    //                     ->where('id_emp', getEmpresa())
    //                     ->where('id', $anamnese->id)
    //                     ->value('descr');

    //         $anamnese->perguntas = DB::table('anamnese_pergunta')
    //                                 ->where('id_anamnese', $id)
    //                                 ->get();

    //         $anamnese->respostas = DB::table('anamnese_resposta')
    //                                 ->where('id_anamnese', $id)
    //                                 ->get();

    //         foreach ($anamnese->perguntas as $pergunta) {
    //             if ($pergunta->tipo == 'C') {
    //                 $pergunta->opcoes = DB::table('anamnese_opcao')
    //                                     ->where('id_pergunta', $pergunta->id)
    //                                     ->get();
    //             }
    //         }

    //         return json_encode($anamnese);
    //     } catch(\Exception $e) {
    //         return $e->getMessage();
    //     }
    // }



    function mostrar_resposta($id) {
        try {
            $anamnese = new \stdClass;
            $aux = DB::select(DB::raw("
                SELECT *, DATE_FORMAT(data, '%d/%m/%Y') AS dataformatada
                FROM anamnese_pessoa
                WHERE id = ".$id
            ));
            $anamnese_pessoa = $aux[0];
            $anamnese->id = $anamnese_pessoa->id_anamnese;
            $anamnese->descr = DB::table('anamnese')
                        // ->where('id_emp', getEmpresa())
                        ->where('id', $anamnese->id)
                        ->value('descr');

            $anamnese->perguntas = DB::table('anamnese_pergunta')
                                    ->select('id','pergunta')
                                    ->where('id_anamnese', $anamnese->id)
                                    ->get();
            $respostas = array();
            foreach($anamnese->perguntas as $pergunta){
                $resposta = DB::table('anamnese_resposta')
                                    ->where('id_pergunta', $pergunta->id)
                                    ->where('id_anamnese_pessoa', $id)
                                    ->value('resposta');
                array_push($respostas, $resposta);
            }
            
            $membro = DB::table('pessoa')
                    ->where('id', $anamnese_pessoa->id_membro)
                    ->value('nome_fantasia');

            $pessoa = DB::table('pessoa')
                    ->where('id', $anamnese_pessoa->id_pessoa)
                    ->value('nome_fantasia');

            

            // foreach ($anamnese->perguntas as $pergunta) {
            //     if ($pergunta->tipo == 'C') {
            //         $pergunta->opcoes = DB::table('anamnese_opcao')
            //                             ->where('id_pergunta', $pergunta->id)
            //                             ->get();
            //     }
            // }

            return view('reports.impresso_anamnese', compact('anamnese', 'respostas', 'membro', 'pessoa', 'anamnese_pessoa'));
            //return json_encode($anamnese->respostas);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function visualizar_anamnese($id) {
        try {
            $anamnese = new \stdClass;
            $anamnese_pessoa = AnamnesePessoa::find($id);
            $anamnese->id = $anamnese_pessoa->id_anamnese;
            $anamnese->descr = DB::table('anamnese')
                        // ->where('id_emp', getEmpresa())
                        ->where('id', $anamnese->id)
                        ->value('descr');

            $anamnese->perguntas = DB::table('anamnese_pergunta')
                                    ->select('id','pergunta')
                                    ->where('id_anamnese', $anamnese->id)
                                    ->get();
            $anamnese->respostas = array();
            foreach($anamnese->perguntas as $pergunta){
                $respostas = DB::table('anamnese_resposta')
                                    ->where('id_pergunta', $pergunta->id)
                                    ->where('id_anamnese_pessoa', $id)
                                    ->get();
                foreach($respostas as $resposta) {
                    array_push($anamnese->respostas, $resposta);
                }
            }
            
            $anamnese->membro = DB::table('pessoa')
                    ->where('id', $anamnese_pessoa->id_membro)
                    ->value('nome_fantasia');

            
            return json_encode($anamnese);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }




    public function responder_anamnese(Request $request) {
        try {
            $anamnese_pessoa = new AnamnesePessoa;
            $anamnese_pessoa->id_emp = getEmpresa();
            $anamnese_pessoa->id_anamnese = $request->id_anamnese;
            $anamnese_pessoa->id_membro = getProfissional()->id;
            $anamnese_pessoa->id_pessoa = $request->id_paciente;
            $anamnese_pessoa->publico = true;
            $anamnese_pessoa->data = date('Y-m-d');
            $anamnese_pessoa->hora = date('H:i:s');
            $anamnese_pessoa->save();

            foreach ($request->respostas as $resposta) {
                $resposta = (object) $resposta;
                if ($resposta->tipo == 'C') {
                    foreach ($resposta->resposta as $cbx_resp) {
                        $ar = new AnamneseResposta;
                        $ar->id_emp = getEmpresa();
                        $ar->id_anamnese_pessoa = $anamnese_pessoa->id;
                        $ar->id_anamnese = $request->id_anamnese;
                        $ar->id_pergunta = $resposta->id_pergunta;
                        $ar->resposta = $cbx_resp;
                        $ar->save();
                    }
                } else {
                    $ar = new AnamneseResposta;
                    $ar->id_emp = getEmpresa();
                    $ar->id_anamnese_pessoa = $anamnese_pessoa->id;
                    $ar->id_anamnese = $request->id_anamnese;
                    $ar->id_pergunta = $resposta->id_pergunta;
                    $ar->resposta = $resposta->resposta;
                    $ar->save();
                }
            }
            return json_encode($anamnese_pessoa);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function desativar(Request $request){
        $anamnese = Anamnese::find($request->id);
        $anamnese->ativo = false;
        $anamnese->save();
        return json_encode($anamnese);
    }
    public function ativar(Request $request){
        $anamnese = Anamnese::find($request->id);
        $anamnese->ativo = true;
        $anamnese->save();
        return json_encode($anamnese);
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('anamnese')
                // ->where('id_emp', getEmpresa())
                ->where('ativo', true)
                ->get()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    
}
