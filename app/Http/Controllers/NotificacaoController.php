<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Notificacao;
use App\Pessoa;
use App\NotificacaoVisualizacoes;
use Illuminate\Http\Request;
use Exception;

class NotificacaoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function listar() {
        $data = new \stdClass;
        $data->notificacoes_ar = array();
        $data->notificacoes_n_visualizadas = array();
        $data->notificacoes = DB::table('notificacao')
                             ->select('notificacao.id             AS id_notificacao', 
                                     'notificacao.id_emp          AS id_empresa',
                                     'notificacao.id_paciente     AS id_paciente',
                                     'pessoa.nome_fantasia        AS descr_paciente', 
                                     'profissional.nome_fantasia  AS descr_profissional',
                                     'notificacao.assunto         AS assunto',
                                     'notificacao.publico         AS publico',
                                     'notificacao.id_profissional AS id_profissional',
                                     'notificacao.created_by      AS created_by',
                                     'notificacao.created_at      AS created_at')
                             ->join('pessoa', 'pessoa.id', 'notificacao.id_paciente')
                             ->leftjoin('pessoa AS profissional', 'profissional.id', 'notificacao.created_by')
                             ->where('notificacao.lixeira', false)
                             ->where('notificacao.id_emp', getEmpresa())
                             ->orderBy('created_at', 'DESC')
                             ->get();
        foreach($data->notificacoes as $notificacao) {
            $visualizacoes = DB::table('notificacao_visualizacoes')
                                ->where('id_user', Auth::user()->id_profissional)
                                ->where('id_notificacao', $notificacao->id_notificacao)
                                //->where('lixeira', false)
                                ->get();
            $mostrar = sizeof($visualizacoes) ? !$visualizacoes[0]->lixeira : 1;
            if ($mostrar && (
                $notificacao->publico || 
                $notificacao->id_profissional == Auth::user()->id_profissional ||
                $notificacao->created_by      == Auth::user()->id_profissional
            )) {
                $notificacao->viz = sizeof($visualizacoes) ? 1 : 0;
                array_push($data->notificacoes_ar, $notificacao);
            }
            /*if ((!$visualizacoes || ($visualizacoes && !$visualizacoes->lixeira))
                && (
                    $notificacao->publico || 
                    $notificacao->id_profissional == Auth::user()->id_profissional ||
                    $notificacao->created_by      == Auth::user()->id_profissional
                   )
            ) {
                array_push($data->notificacoes_ar, $notificacao);
                try {
                    array_push($data->notificacoes_n_visualizadas, $visualizacoes->visualizado);
                } catch(Exception $e) {}
            }*/
        }
        return json_encode($data);
    }

    public function salvar(Request $request){
        $notificacao = new Notificacao;
        $notificacao->id_emp = getEmpresa();
        $notificacao->id_paciente = $request->associado;
        $notificacao->assunto = $request->assunto;
        $notificacao->notificacao = $request->notificacao;
        if ($request->publico == 'S'){
            $notificacao->publico = false;
            $notificacao->id_profissional = $request->profissional;
        }
        else {
            $notificacao->publico = true;
            $notificacao->id_profissional = 0;
        }
        $notificacao->created_by = Auth::user()->id_profissional;
        $notificacao->lixeira = false;
        $notificacao->save();
        return 'true';
    }

    public function excluir (Request $request){
        $query = "
            SELECT id
            FROM notificacao_visualizacoes
            WHERE id_user = ".Auth::user()->id_profissional."
              AND id_notificacao = ".$request->id
        ;

        $consulta = DB::select(DB::raw($query));
        $notificacao = $consulta[0]->id;

        /*$notificacao = DB::table('notificacao_visualizacoes')
        ->where('id_user', Auth::user()->id_profissional)
        ->where('id_notificacao', $request->id)
        ->value('id');*/

        $visualizacao = NotificacaoVisualizacoes::find($notificacao);
        //return json_encode($visualizacao);
        $visualizacao->lixeira = true;
        $visualizacao->save();

        return 'true';
    }

    public function listarPorPessoa($id){
        $notificacoes_ar = array();
        $notificacoes = DB::table('notificacao')
                        ->select('id              AS id_notificacao', 
                                 'id_emp          AS id_empresa',
                                 'id_paciente     AS id_paciente', 
                                 'assunto         AS assunto',
                                 'publico         AS publico',
                                 'id_profissional AS id_profissional',
                                 'created_by      AS created_by',
                                 'created_at      AS created_at')
                        ->where('lixeira', false)
                        ->where('id_paciente', $id)
                        ->orderBy('created_at', 'DESC')
                        ->get();
        foreach($notificacoes as $notificacao){
            if ($notificacao->publico == 1){
                array_push($notificacoes_ar, $notificacao);
            }
            else {
                if (($notificacao->id_profissional == Auth::user()->id_profissional ||
                    $notificacao->created_by      == Auth::user()->id_profissional)){
                        array_push($notificacoes_ar, $notificacao);
                    }
            }
        }
        return $notificacoes_ar;
    }

    function visualizar_notificacao($id) {
        $notificacao = DB::table('notificacao_visualizacoes')
                        ->where('id_user', Auth::user()->id_profissional)
                        ->where('id_notificacao', $id)
                        ->value('id');
        if (!$notificacao){
            $visualizacao = new NotificacaoVisualizacoes;
        }
        else $visualizacao = NotificacaoVisualizacoes::find($notificacao);

        $visualizacao->id_user = Auth::user()->id_profissional;
        $visualizacao->id_notificacao = $id;
        $visualizacao->visualizado = true;
        $visualizacao->lixeira = false;
        $visualizacao->save();

        $noti = Notificacao::find($id);
        return $noti;
    }
}