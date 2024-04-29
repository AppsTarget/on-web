<?php

namespace App\Http\Controllers;

use DB;
use App\HistoricoAgenda;
use Illuminate\Http\Request;

class HistoricoAgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $agenda_refer = DB::table('agenda')
                            ->where('id', $request->id_agenda)
                            ->first();

            $historico = new HistoricoAgenda;
            $historico->id_emp = getEmpresa();
            $historico->id_agenda = $request->id_agenda;
            $historico->id_status = $agenda_refer->id_status;
            $historico->id_tipo_procedimento = $agenda_refer->id_tipo_procedimento;
            $historico->id_procedimento = $agenda_refer->id_procedimento;
            $historico->id_tipo_confirmacao = $agenda_refer->id_confirmacao;
            $historico->campo = $request->campo;
            $historico->save();
            
            return $historico_status;
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }

    public function listar(Request $request) {
        try {
            $historico = DB::table('historico_agenda')
                        ->select(
                            'historico_agenda.id_agenda',
                            'historico_agenda.id_status',
                            'historico_agenda.id_tipo_procedimento',
                            'historico_agenda.id_procedimento',
                            'historico_agenda.id_tipo_confirmacao',
                            'historico_agenda.campo',
                            'agenda_confirmacao.descr AS descr_tipo_confirmacao',
                            'tipo_procedimento.descr AS descr_tipo_procedimento',
                            'pessoa.nome_fantasia AS nome_paciente',
                            'agenda_status.descr AS descr_status',
                            'historico_agenda.created_at AS data',
                            'historico_agenda.created_by'
                        )
                        ->leftjoin('agenda', 'agenda.id', 'historico_agenda.id_agenda')
                        ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'historico_agenda.id_tipo_procedimento')
                        ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                        ->leftjoin('agenda_status', 'agenda_status.id', 'historico_agenda.id_status')
                        ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'historico_agenda.id_tipo_confirmacao')
                        ->where('historico_agenda.id_emp', getEmpresa())
                        ->where('historico_agenda.id_agenda', $request->id_agenda)
                        ->where(function($sql) use($request) {
                            if (isset($request->campo)) {
                                $sql->where('historico_agenda.campo', $request->campo);
                            }
                        })
                        ->groupby(
                            'historico_agenda.id_agenda',
                            'historico_agenda.id_status',
                            'historico_agenda.id_tipo_procedimento',
                            'historico_agenda.id_procedimento',
                            'historico_agenda.id_tipo_confirmacao',
                            'historico_agenda.campo',
                            'agenda_confirmacao.descr',
                            'tipo_procedimento.descr',
                            'pessoa.nome_fantasia',
                            'agenda_status.descr',
                            'historico_agenda.created_at',
                            'historico_agenda.created_by'
                        )
                        ->orderby('historico_agenda.created_at')
                        ->get();

            return json_encode($historico);
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }
}