<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\IECPessoa;
use App\IECPessoaResp;
use App\IECQuestao;
use App\IECQuestaoArea;
use App\IECQuestionario;
use Illuminate\Http\Request;

class IECController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function criar() {
        $especialidades = DB::table('especialidade')->where('lixeira', '<>', 1)->get();
        return view('criar_IEC', compact('especialidades'));
    }
    public function editar($id) {
        $especialidades = DB::table('especialidade')->where('lixeira', '<>', 1)->get();
        return view('editar_IEC', compact('especialidades', 'id'));
    }
    public function listar() {
        $IECs = DB::table("IEC_questionario")
                // ->where('id_emp', getEmpresa())
                ->where('IEC_questionario.lixeira', '<>', 'S')
                ->orderBy('IEC_questionario.descr')
                ->get();
        return view('IEC', compact("IECs"));
    }
    public function salvar(Request $request) {
        try {
            $j = 0;
            if($request->id != 0) {
                $IEC_antigo = IECQuestionario::find($request->id);
                $IEC_antigo->descr = $IEC_antigo->descr. '  -  ' .date('d/m/Y');
                $IEC_antigo->save();
            }
            $IEC = new IECQuestionario;
            
            $IEC->id_emp = getEmpresa();
            $IEC->descr = $request->descr;
            $IEC->ativo = 'S';
            $IEC->lixeira = 'N'; 

            $IEC->save();
            foreach ($request->perguntas as $pergunta) {
                $i = 0;
                $ap = new IECQuestao;
                $ap->id_questionario = $IEC->id;
                $ap->pergunta = trim($pergunta['descr']);
                $ap->obs = trim($pergunta['obs']);
                $ap->pessimo   = $pergunta['opcoes'][0];
                $ap->ruim      = $pergunta['opcoes'][1];
                $ap->bom       = $pergunta['opcoes'][2];
                $ap->excelente = $pergunta['opcoes'][3];
                $ap->save();

                $auxiliar = $pergunta['areas'];
                foreach ($auxiliar as $area) {
                    $arr = explode(',', $area);
                    $i++;
                    foreach($arr as $id_especialidade){
                        $op = new IECQuestaoArea;
                        $op->id_questao = $ap->id;
                        $op->id_area    = $id_especialidade;
                        $op->status     = $i; 
                        $op->save();
                        $j++;
                    }
                    
                }
            }
            return 'true';
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function exibir_para_editar($id){
        $data = new \stdClass;
        $data->IEC_questao = DB::table('IEC_questao')
                       ->where('id_questionario', $id)
                       ->get();
        $data->descricao = DB::table('IEC_questionario')
                        ->where('id', $id)
                        ->value('descr');
        $data->areas = array();

        foreach($data->IEC_questao as $IEC_questao){
            for ($i=1; $i<5; $i++){
                $aux = '';
                $areas_da_questao = DB::table('IEC_questao_area')
                                    ->where('id_questao', $IEC_questao->id)
                                    ->where('status', $i)
                                    ->get();
                if (sizeof($areas_da_questao) != 0){
                    foreach($areas_da_questao as $ar){
                        $aux = $aux . strval($ar->id_area) . ',';
                    }
                    array_push($data->areas, $aux);
                }
                else array_push($data->areas, '');
            }
        }
        return json_encode($data);
    }
    public function listarPorPessoa($id_pessoa, $inativo) {
        try {
            if ($inativo == 'L') {
                $IEC_pessoas = DB::select(
                    DB::raw("
                        SELECT
                            laudo.updated_at,
                            laudo.created_at,
                            laudo.id,
                            laudo.id AS id_questionario,
                            CONCAT('LAUDO - ', DATE_FORMAT(laudo.created_at, '%d/%m/%Y'), ' | ', profissional.nome_fantasia) as descr_iec,
                            (select 0) AS piores,
                            pessoa.nome_fantasia AS descr_pessoa,
                            pessoa.id AS id_pessoa
                        FROM
                            laudo
                            left join pessoa on pessoa.id = laudo.id_prof
                            left join pessoa AS profissional on profissional.id = laudo.id_prof
                        WHERE
                            laudo.id_pessoa = " . $id_pessoa ." AND
                            laudo.dump = 0
                        ORDER BY 
                            laudo.created_at DESC
                    ")
                );
            }
            else {
                $IEC_pessoas = DB::select(
                    DB::raw(
                        "SELECT 
                            IEC_pessoa.*,
                            IEC_questionario.id As id_questionario,
                            IEC_questionario.descr AS descr_iec,
                            pessoa.nome_fantasia As descr_pessoa,
                            pessoa.id as id_pessoa,
                            MIN(IEC_pessoa_resp.resposta) AS piores
                        FROM
                            IEC_pessoa
                            left join IEC_questionario on IEC_questionario.id = IEC_pessoa.id_questionario
                            left join pessoa on pessoa.id = IEC_pessoa.id_paciente
                            left join IEC_pessoa_resp on IEC_pessoa_resp.id_iec_pessoa = IEC_pessoa.id
                        WHERE 
                            IEC_pessoa.id_paciente = ". $id_pessoa ." AND
                            IEC_questionario.lixeira <> 'S' AND
                            (IEC_questionario.ativo = '".$inativo."') AND
                            IEC_pessoa.lixeira = 0
                        GROUP BY
                            IEC_pessoa.id_questionario, IEC_pessoa.id_membro, IEC_pessoa.id_paciente, IEC_pessoa.destacar, IEC_pessoa.obs, IEC_pessoa.lixeira, IEC_pessoa.id,
                            IEC_questionario.id,
                            IEC_questionario.descr,
                            pessoa.nome_fantasia,
                            pessoa.id
                        ORDER BY
                            IEC_pessoa.updated_at DESC
                        "
                    )
                );
            }
            
            return $IEC_pessoas;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function histIEC($json) {
        return view("grafico_hist_iec", compact("json"));
    }
    public function listarPorPessoaGrafico($id_pessoa) {
        try {
            $IEC_pessoas = DB::table('IEC_pessoa')
                        ->select(
                            'IEC_pessoa.*',
                            'IEC_questionario.id as id_questionario',
                            'IEC_questionario.descr AS descr_iec',
                            'paciente.nome_fantasia AS descr_pessoa',
                            'profissional.nome_fantasia AS descr_membro',
                            'updated_at'
                        )
                        ->leftjoin('IEC_questionario', 'IEC_questionario.id', 'IEC_pessoa.id_questionario')
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'IEC_pessoa.id_paciente')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'IEC_pessoa.id_membro')
                        ->where('IEC_pessoa.id_paciente', $id_pessoa)
                        ->where('IEC_questionario.lixeira', '<>', 'S')
                        ->where('updated_at', '>', date('Y-m-d', strtotime('-6 months')))
                        ->orderBy('updated_at', 'DESC')
                        ->get();
            $piores = array();
            foreach($IEC_pessoas as $IEC_pessoa){
                $pior_resposta = DB::table('IEC_pessoa_resp')
                                ->where('id_iec_pessoa', $IEC_pessoa->id)
                                ->min('resposta');
                array_push($piores, $pior_resposta);
            }
            $IEC_pessoas->pior_area = $piores;
            
            return compact('piores','IEC_pessoas');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function mostrar_resposta($id) {
        try {
            $IEC = new \stdClass;
            $IEC_pessoa = IECPessoa::find($id);
            $IEC->id = $IEC_pessoa->id_questionario;
            $IEC->descr = DB::table('IEC_questionario')
                // ->where('id_emp', getEmpresa())
                        ->where('id', $IEC->id)
                        ->value('descr');
            $obs = $IEC_pessoa->obs;
            $IEC->perguntas = DB::table('IEC_questao')
                                    ->select('id','pergunta')
                                    ->where('id_questionario', $IEC->id)
                                    ->get();
            $respostas = array();
            $valores = array();
            $id_areas_sugeridas = array();

            foreach($IEC->perguntas as $pergunta){
                
                $j = 0;
                $resposta = DB::table('IEC_pessoa_resp')
                                    ->where('id_questao', $pergunta->id)
                                    ->where('id_iec_pessoa', $id)
                                    ->value('resposta');
                $resposta_str = IECQuestao::find($pergunta->id);
                $area = DB::table('IEC_questao_area')
                        ->where('id_questao', $pergunta->id)
                        ->where('status', $resposta)
                        ->get();
                        
                array_push($id_areas_sugeridas, $area);
                array_push($valores,   $resposta);
                switch($resposta){
                    case 1:
                        array_push($respostas, $resposta_str->pessimo);
                        break;
                    case 2:
                        array_push($respostas, $resposta_str->ruim);                        
                        break;
                    case 3:
                        array_push($respostas, $resposta_str->bom);                        
                        break;
                    case 4:
                        array_push($respostas, $resposta_str->excelente);
                        break;
                }
                
            }
            
            $membro = DB::table('pessoa')
                    ->where('id', $IEC_pessoa->id_membro)
                    ->value('nome_fantasia');

            $pessoa = DB::table('pessoa')
                    ->where('id', $IEC_pessoa->id_paciente)
                    ->value('nome_fantasia');
                    // return json_encode($respostas);
            return view('reports.impresso_IEC', compact('IEC', 'respostas', 'membro', 'pessoa', 'anamnese_pessoa', 'valores', 'id_areas_sugeridas', 'obs'));
            
            return json_encode($anamnese->respostas);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    function visualizar_resposta($id) {
        try {
            $IEC = new \stdClass;
            $IEC_pessoa = IECPessoa::find($id);
            $IEC->id = $IEC_pessoa->id_questionario;
            $IEC->obs = $IEC_pessoa->obs;

            $IEC->descr = DB::table('IEC_questionario')
                        // ->where('id_emp', getEmpresa())
                        ->where('id', $IEC->id)
                        ->value('descr');


            $IEC->perguntas = DB::table('IEC_questao')
                                ->select('id','pergunta')
                                ->where('id_questionario', $IEC->id)
                                ->get();
            $IEC->respostas = array();
            $IEC->valores = array();
            $IEC->id_areas_sugeridas = array();

            foreach($IEC->perguntas as $pergunta){
                
                $j = 0;
                $resposta = DB::table('IEC_pessoa_resp')
                                    ->where('id_questao', $pergunta->id)
                                    ->where('id_iec_pessoa', $id)
                                    ->value('resposta');
                $resposta_str = IECQuestao::find($pergunta->id);
                $area = DB::table('IEC_questao_area')
                        ->where('id_questao', $pergunta->id)
                        ->where('status', $resposta)
                        ->get();
                        
                array_push($IEC->id_areas_sugeridas, $area);
                array_push($IEC->valores,   $resposta);
                switch($resposta){
                    case 1:
                        array_push($IEC->respostas, $resposta_str->pessimo);
                        break;
                    case 2:
                        array_push($IEC->respostas, $resposta_str->ruim);                        
                        break;
                    case 3:
                        array_push($IEC->respostas, $resposta_str->bom);                        
                        break;
                    case 4:
                        array_push($IEC->respostas, $resposta_str->excelente);
                        break;
                }
                
            }
            
            return json_encode($IEC);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function favoritar($id){
        $IEC_pessoa = IECPessoa::find($id);
        $IEC_pessoa->destacar = $IEC_pessoa->destacar == 'S' ? 'N' : 'S';
        $IEC_pessoa->save();
        return 'true';
    }
    public function ativar($id){
        $IEC_questionario = IECQuestionario::find($id);
        if ($IEC_questionario->ativo == 'S'){ 
            $IEC_questionario->ativo = 'N';
            $IEC_questionario->save();
            return 'desativado';
        }
        else {
            $IEC_questionario->ativo = 'S';
            $IEC_questionario->save();
            return 'ativado';
        }
    }

    public function excluir($id){
        $IEC_questionario = IECQuestionario::find($id);
        $IEC_questionario->lixeira = 'S';
        $IEC_questionario->save();
        return 'true';
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('IEC_questionario')
                // ->where('id_emp', getEmpresa())
                ->where('ativo', 'S')
                ->where('lixeira', 'N')
                ->get()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function responder_iec(Request $request) {
        try {
            $IEC_pessoa = new IECPessoa;
            $IEC_pessoa->id_questionario = $request->id_iec;
            $IEC_pessoa->id_membro = getProfissional()->id;
            $IEC_pessoa->id_paciente = $request->id_paciente;
            $IEC_pessoa->obs = $request->obs;
            $IEC_pessoa->destacar = 'N';
            $IEC_pessoa->id_emp = getEmpresa();
            $IEC_pessoa->save();

            foreach ($request->respostas as $resposta) {
                $resposta = (object) $resposta;
                    foreach ($resposta->resposta as $cbx_resp) {
                        $ar = new IECPessoaResp;
                        $ar->id_iec_pessoa = $IEC_pessoa->id;
                        $ar->id_questao = $resposta->id_pergunta;
                        $ar->resposta = $cbx_resp[0] + 1;
                        $ar->save();
                } 
            }
            return json_encode($IEC_pessoa);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    function mostrar_iec($id) {
        try {
            $IEC = new \stdClass;
            $IEC->id = $id;
            $IEC->descr = DB::table('IEC_questionario')
                        // ->where('id_emp', getEmpresa())
                        ->where('id', $id)
                        ->value('descr');

            $IEC->perguntas = DB::table('IEC_questao')
                                    ->where('id_questionario', $id)
                                    ->get();

            return json_encode($IEC);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function deletar(Request $request) {
        DB::statement("
            CREATE TEMPORARY TABLE tmp_table AS (
                SELECT
                    id_questionario,
                    id_paciente
                FROM IEC_pessoa
                WHERE id = ".$request->id_iec_pessoa."
            )
        ");
        DB::statement("
            UPDATE
                IEC_pessoa
                JOIN tmp_table
                    ON IEC_pessoa.id_questionario = tmp_table.id_questionario
                        AND IEC_pessoa.id_paciente = tmp_table.id_paciente
            SET IEC_pessoa.lixeira = 1
        ");
        DB::statement("DROP TABLE tmp_table");
    }
    public function carregar($id) {
        $data = new \stdClass;
        $data->iec  = DB::select(DB::raw("SELECT * FROM IEC_pessoa      WHERE id = ".$id));
        $data->resp = DB::select(DB::raw("SELECT * FROM IEC_pessoa_resp WHERE id_iec_pessoa = ".$id));
        return json_encode($data);
    }
    public function historico_iec ($id){
        $iec = IECPessoa::find($id);

        try {
            $IEC_pessoas = DB::table('IEC_pessoa')
                        ->select(
                            'IEC_pessoa.*',
                            'IEC_questionario.id as id_questionario',
                            'IEC_questionario.descr AS descr_iec',
                            'paciente.nome_fantasia AS descr_pessoa',
                            'profissional.nome_fantasia AS descr_membro'
                        )
                        ->leftjoin('IEC_questionario', 'IEC_questionario.id', 'IEC_pessoa.id_questionario')
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'IEC_pessoa.id_paciente')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'IEC_pessoa.id_membro')
                        ->where('IEC_pessoa.id_paciente', $iec->id_paciente)
                        ->where("IEC_questionario.id", $iec->id_questionario)
                        ->where('IEC_questionario.lixeira', '<>', 'S')
                        ->where('IEC_pessoa.lixeira', 0)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
            $piores = [];
            $datas = [];
            foreach($IEC_pessoas as $IEC_pessoa){
                $pior_resposta = DB::select(DB::raw("
                    SELECT
                        DAYOFYEAR(IEC_pessoa.created_at) AS data,
                        MIN(resposta) AS pior
                    
                    FROM IEC_pessoa_resp

                    LEFT JOIN IEC_pessoa
                        ON IEC_pessoa.id = IEC_pessoa_resp.id_iec_pessoa

                    WHERE id_iec_pessoa = ".$IEC_pessoa->id."

                    GROUP BY data
                "));
                foreach ($pior_resposta as $resposta) {
                    if (!in_array($resposta->data, $datas)) {
                        $piores["d".$resposta->data] = $resposta->pior;
                        array_push($datas, $resposta->data);
                    }
                }
            }
            $IEC_pessoas->pior_area = $piores;
            
            return compact('piores','IEC_pessoas');
        } catch(\Exception $e) {
            return $e->getMessage();
        }                  
    }
    public function listar_areas_recomendadas($id_pessoa) {
        $areas = DB::table('IEC_questao_area')
                 ->select('IEC_questao_area.id_area')
                 ->join('IEC_questao', 'IEC_questao.id', 'IEC_questao_area.id_questao')
                 ->join('IEC_questionario', 'IEC_questionario.id', 'IEC_questao.id_questionario')
                 ->join('IEC_pessoa', 'IEC_pessoa.id_questionario', 'IEC_questionario.id')
                 ->where('IEC_pessoa.id_paciente', $id_pessoa)
                 ->groupBy('IEC_questao_area.id_area')
                 ->get();
        return json_encode($areas);
    }
}