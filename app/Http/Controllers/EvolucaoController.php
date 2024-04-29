<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Evolucao;
use Illuminate\Http\Request;

class EvolucaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if ($request->id_evolucao <> 0) $evolucao = Evolucao::find($request->id_evolucao);
            else                            $evolucao = new Evolucao;
            
            $evolucao->id_emp = getEmpresa();
            $evolucao->id_evolucao_tipo = $request->id_evolucao_tipo;
            $evolucao->id_corpo = $request->id_parte_corpo;
            $evolucao->data = implode('-', array_reverse(explode('/', $request->data)));
            $evolucao->hora = $request->hora;

            if (getEmpresaObj()->tipo == 'M') {
                $evolucao->titulo = $request->titulo_evolucao;
                $evolucao->cid = $request->cid;
                $evolucao->estado = $request->estado;
            }
            $evolucao->id_area = $request->especialidade;
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
            $evolucao = Evolucao::find($request->id);
            if (Auth::user()->id_profissional != $evolucao->id_profissional)return ["error" => 'Somente o criador deste registro pode excluí-lo'];
            $evolucao->lixeira = 1;
            $evolucao->save();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function tornar_privado(Request $request) {
        try{
            $evolucao = Evolucao::find($request->id);
            $evolucao->publico = 'N';
            $evolucao->save(); 

            return 'privado';

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function tornar_publico(Request $request) {
        try {
            $evolucao = Evolucao::find($request->id);
            $evolucao->publico = 'S';
            $evolucao->save();

            return 'publico';

        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function mostrar($id_evolucao) {
        return json_encode(Evolucao::find($id_evolucao));
    }

    public function listar($id_paciente) {
        try {
            $evolucoes = DB::table('evolucao')
                            ->where('id_paciente', $id_paciente)
                            ->where('lixeira', 0)
                            ->get();

            return $evolucoes;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa) {
        try {
            $data = new \StdClass();
            $data->evolucoes = DB::table('evolucao')
            
                        ->select(
                            'evolucao.*',
                            'profissional.nome_fantasia AS descr_profissional',
                            'paciente.nome_fantasia AS descr_paciente',
                            'evolucao_tipo.descr AS descr_evolucao_tipo',
                            'evolucao_tipo.prioritario'
                        )
                        ->leftjoin('evolucao_tipo', 'evolucao_tipo.id', 'evolucao.id_evolucao_tipo')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'evolucao.id_profissional')
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'evolucao.id_paciente')
                        ->where('evolucao.id_paciente', $id_pessoa)
                        ->where('evolucao.lixeira', 0)
                        ->where(function($sql) {
                            $sql->where('evolucao.publico', 'S')
                                ->orWhere('evolucao.id_profissional', Auth::user()->id_profissional);
                        })
                        ->orderby('evolucao.data', 'DESC')
                        ->orderby('evolucao.hora', 'DESC')
                        ->get();

            $data->profissional = Auth::user()->id_profissional;

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }


    public function listarAgendamentos ($id_paciente){

        $pedidos = DB::table('pedido')
        ->selectRaw('Group_Concat(pedido.id) AS ids')
        ->where('lixeira', 0)
        ->where('status', 'F')
        ->value('ids');
        $pedidos = "(" . $pedidos . ")";

        $agendamentos = DB::table('agenda')
                            ->select(
                                'agenda.id',
                                'agenda.id_emp',
                                'agenda.id_status',
                                'agenda.status',
                                'agenda.id_tipo_procedimento',
                                'tipo_procedimento.descr AS tipo_procedimento',
                                'agenda.id_grade_horario',
                                'agenda.hora',
                                'agenda.data',
                                'agenda.id_paciente',
                                'agenda.id_profissional',
                                'pessoa.nome_fantasia AS nome_paciente',
                                
                            
                            
                                DB::raw(
                                    '(CASE' .
                                    '   WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido ' .
                                    '   ELSE profissional.nome_fantasia ' .
                                    'END) AS nome_profissional'
                                ),
                                'procedimento.descr AS descr_procedimento',
                                'convenio.descr AS convenio_nome',
                                'agenda_status.permite_editar',
                                'agenda_status.libera_horario',
                                'agenda_status.permite_fila_espera',
                                'agenda_status.permite_reagendar',
                                'agenda_status.descr AS descr_status',
                                'agenda_status.cor AS cor_status',
                                'agenda_status.cor_letra',
                                'grade.min_intervalo',
                                'agenda.id_confirmacao',
                                'agenda_confirmacao.descr AS descr_confirmacao',
                                DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs"),
                                DB::raw("(select 0) AS antigo")
                                )
                                // ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                                // ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                                // ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                                // ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
        
                                // ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                // ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                                // ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                                // ->whereRaw('(agenda.id_pedido in'. $pedidos ."AND agenda.lixeira = 0 AND agenda.status = 'A')")

                                // ->where('agenda.id_profissional', Auth::user()->id_profissional)
                                // ->where('agenda.data', date('Y-m-d'))
                                
                                // ->where('agenda.status', 'A')
                                // ->where('agenda.id_emp', getEmpresa())
                                // ->where('agenda.lixeira', 0)
                                // ->where('agenda.id_paciente', $id_paciente)
                                
                
                                ->get();

    return json_encode($agendamentos);
    }


    public function listarAgendas ($id_paciente){
       
        $agendamentos = DB::table('agenda')
                            ->select(
                                'agenda.id',
                                'agenda.id_emp',
                                'agenda.id_status',
                                'agenda.status',
                                'agenda.id_tipo_procedimento',
                                'tipo_procedimento.descr AS tipo_procedimento',
                                'agenda.id_grade_horario',
                                'agenda.hora',
                                'agenda.data',
                                'agenda.id_paciente',
                                'agenda.id_profissional',
                                'pessoa.nome_fantasia AS nome_paciente',
                                
                            
                            
                                DB::raw(
                                    '(CASE' .
                                    '   WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido ' .
                                    '   ELSE profissional.nome_fantasia ' .
                                    'END) AS nome_profissional'
                                ),
                                'procedimento.descr AS descr_procedimento',
                                'convenio.descr AS convenio_nome',
                                'agenda_status.permite_editar',
                                'agenda_status.libera_horario',
                                'agenda_status.permite_fila_espera',
                                'agenda_status.permite_reagendar',
                                'agenda_status.descr AS descr_status',
                                'agenda_status.cor AS cor_status',
                                'agenda_status.cor_letra',
                                'grade.min_intervalo',
                                'agenda.id_confirmacao',
                                'agenda_confirmacao.descr AS descr_confirmacao',
                                DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs"),
                                DB::raw("(select 0) AS antigo")
                                )
                                ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                                ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                                ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                                
                                ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
                                ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                                ->leftjoin('fila_espera', 'fila_espera.id_agendamento', 'agenda.id')
                                ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                                ->where('agenda.status', 'A')
                                ->where('agenda.lixeira', 0)
                                ->where('agenda.data', date('Y-m-d'))
                                ->where('agenda.id_paciente', $id_paciente)
                                ->where('agenda.id_profissional', Auth::user()->id_profissional)
                                //->unionAll(DB::table('old_mov_atividades'))
                               
                                ->get();    
    
        return $agendamentos;
        }
    
}


    

