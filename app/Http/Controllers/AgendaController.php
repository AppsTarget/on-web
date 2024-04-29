<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Agenda;
use App\AgendaStatus;
use App\AgendaConfirmados;
use App\TipoProcedimento;
use App\PedidoFormaPag;
use App\Pessoa;
use App\Pedido;
use App\Procedimento;
use GuzzleHttp\Client;
use App\ZEnvia;
use App\Empresa;
use App\Encaminhamento;
use App\GradeHorario;
use App\TabelaPrecos;
use App\PedidoParcela;
use App\HistoricoAgenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request)
    {
        try {
            $dt = date('Y-m-d', strtotime(str_replace("/", "-", $request->data)));

            if ($request->agenda_encaminhante_id > 0) {
                $enc = new Encaminhamento;
                $enc->id_de = $request->agenda_encaminhante_id;
                $enc->id_para = DB::table("enc2_encaminhantes")->where("id_pessoa", $request->id_profissional)->value("id");
                $enc->id_especialidade = $request->agenda_enc_esp;
                $enc->id_cid = $request->enc_cid_id;
                $enc->id_paciente = $request->id_paciente;
                // $enc->id_solicitacao = $request->agenda_sol;
                if (intval($request->agenda_sol) > 0) {
                    $enc->id_solicitacao = $request->agenda_sol;
                    $consulta = DB::table("enc2_solicitacao")->where("id", $request->agenda_sol)->value("updated_at");
                    $aux = explode(" ", $consulta);
                    $enc->data = $aux[0];
                } else $enc->data = $dt;
                $enc->save();
            }
            
            if (!$request->id) {
                $agendamento = new Agenda;
                $agendamento->id_emp = getEmpresa();
                $agendamento->id_profissional = $request->id_profissional;
                $agendamento->id_paciente = $request->id_paciente;
                $agendamento->id_tipo_procedimento = $request->id_tipo_procedimento;
                $agendamento->id_grade_horario = $request->id_grade_horario;
                $agendamento->id_convenio = $request->id_convenio;
                $agendamento->id_status = 6;
                $agendamento->id_confirmacao = 0;
                $agendamento->id_reagendado = 0;
                $agendamento->id_pedido = $request->id_pedido;
                $agendamento->id_tabela_preco = $request->id_tabela_preco;
                $agendamento->id_modalidade = $request->modalidade_id;
                $agendamento->data = $dt;
                $agendamento->hora = $request->hora;
                $agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
                $agendamento->obs = $request->obs;
                $agendamento->status = 'A';
                $agendamento->motivo_cancelamento = 0;
                $agendamento->obs_cancelamento = '';
                $agendamento->reagendamento = false;
                $agendamento->bordero = true;
                $agendamento->lixeira = false;
                $agendamento->created_by = Auth::user()->name;
                $agendamento->updated_by = Auth::user()->name;
                $agendamento->save();
            } else {
                $agendamento = Agenda::find($request->id);
                if (
                    date('Y-m-d', strtotime(str_replace("/", "-", $request->data))) != $agendamento->data ||
                    substr($agendamento->hora, 0, 5) != $request->hora
                ) {
                    $agendamento_antigo = $agendamento;
                    $agendamento_antigo->id_emp = getEmpresa();
                    $agendamento_antigo->id_status = 16;
                    $agendamento_antigo->updated_by = Auth::user()->name;
                    $agendamento_antigo->updated_at = date('Y-m-d H:i:s');
                    $agendamento_antigo->motivo_cancelamento = 4;
                    $agendamento_antigo->reagendamento = true;
                    $agendamento_antigo->obs_cancelamento = ('Reagendado para ' . $request->data . ' ' . $request->hora);
                    $agendamento_antigo->status = 'C';
                    $agendamento_antigo->save();

                    $agendamento_novo = new Agenda;
                    $agendamento_novo->id_emp = $agendamento_antigo->id_emp;
                    $agendamento_novo->id_profissional = $agendamento_antigo->id_profissional;
                    $agendamento_novo->id_paciente = $agendamento_antigo->id_paciente;
                    $agendamento_novo->id_tipo_procedimento = $agendamento_antigo->id_tipo_procedimento;
                    $agendamento_novo->id_grade_horario = $agendamento_antigo->id_grade_horario;
                    $agendamento_novo->id_convenio = $agendamento_antigo->id_convenio;
                    $agendamento_novo->id_status = 6;
                    $agendamento_novo->id_confirmacao = $agendamento_antigo->id_confirmacao;
                    $agendamento_novo->id_reagendado = $agendamento_antigo->id_reagendado;
                    $agendamento_novo->id_pedido = $agendamento_antigo->id_pedido;
                    $agendamento_novo->id_tabela_preco = $agendamento_antigo->id_tabela_preco;
                    $agendamento_novo->id_modalidade = $agendamento_antigo->id_modalidade;
                    $agendamento_novo->data = $dt;
                    $agendamento_novo->hora = $request->hora;
                    $agendamento_novo->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
                    $agendamento_novo->obs = $request->obs;
                    $agendamento_novo->status = 'A';
                    $agendamento_novo->motivo_cancelamento = 0;
                    $agendamento_novo->obs_cancelamento = '';
                    $agendamento_novo->reagendamento = true;
                    $agendamento_novo->bordero = true;
                    $agendamento_novo->lixeira = false;
                    $agendamento_novo->created_by = Auth::user()->name;
                    $agendamento_novo->updated_by = Auth::user()->name;
                    $agendamento_novo->save();
                } else {
                    $agendamento->id_emp = getEmpresa();
                    $agendamento->id_profissional = $request->id_profissional;
                    $agendamento->id_paciente = $request->id_paciente;
                    $agendamento->id_tipo_procedimento = $request->id_tipo_procedimento;
                    $agendamento->id_grade_horario = $request->id_grade_horario;
                    $agendamento->id_convenio = $request->id_convenio;
                    $agendamento->id_pedido = $request->id_pedido;
                    $agendamento->id_tabela_preco = $request->id_tabela_preco;
                    $agendamento->id_modalidade = $request->modalidade_id;
                    $agendamento->data = $dt;
                    $agendamento->hora = $request->hora;
                    $agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
                    $agendamento->obs = $request->obs;
                    $agendamento->updated_by = Auth::user()->name;
                    $agendamento->save();
                }
            }
            if ($request->agenda_encaminhante_id > 0) {
                $agendamento->id_encaminhamento = $enc->id;
                $agendamento->save();
            }
            return $agendamento->id;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_modalidades_por_plano($id_plano)
    {
        return DB::table('procedimento')
            ->select('procedimento.id', 'procedimento.descr')
            ->leftjoin('modalidades_por_plano', 'modalidades_por_plano.id_procedimento', 'procedimento.id')
            ->where('modalidades_por_plano.id_tabela_preco', $id_plano)
            ->groupBy('procedimento.id', 'procedimento.descr')
            ->get();
    }

    public function statusCasoReagendar()
    {
        return DB::table('agenda_status')
            ->where('id_emp', getEmpresa())
            ->where('case_reagendar', true)
            ->where('lixeira', false)
            ->orderby('updated_at', 'DESC')
            ->first();
    }

    public function validarReagendamento($id)
    {
        $agenda_status = AgendaStatus::find($id);
        return $agenda_status->permite_reagendar;
    }

    public function mostrar()
    {
        $usuario_prof = Pessoa::find(Auth::user()->id_profissional);
        if ($usuario_prof->colaborador == 'P' && $usuario_prof->administrador != 'S') {
            $profissionais = DB::table('pessoa')
                ->where('id', $usuario_prof->id)
                ->get();
        } 
        else if ($usuario_prof->id == 28480002313) {
            $profissionais = DB::table('pessoa')
                ->where('id', 443000000)
                ->get();
        }
        else {
            $profissionais = DB::table('pessoa')
                ->select('pessoa.*')
                ->leftjoin('empresas_profissional', 'empresas_profissional.id_profissional', 'pessoa.id')
                ->where('empresas_profissional.id_emp', getEmpresa())
                ->where(function ($sql) {
                    $sql->where('colaborador', 'P')
                        ->orWhere('colaborador', 'A');
                })
                ->where('lixeira', false)
                // ->groupBy('pessoa.*')
                ->get();
        }

        $fila_espera = DB::table('fila_espera')
            ->select(
                'fila_espera.id',
                'pessoa.id AS paciente_id',
                'pessoa.nome_fantasia AS paciente_nome',
                'agenda.hora',
                'fila_espera.hora_chegada'
            )
            ->leftjoin('pessoa', 'pessoa.id', 'fila_espera.id_paciente')
            ->leftjoin('agenda', 'agenda.id', 'fila_espera.id_agendamento')
            ->where('fila_espera.id_emp', getEmpresa())
            ->where('fila_espera.status', 'E')
            ->where('data_chegada', date('Y-m-d'))
            ->get();

        $convenios = DB::table('convenio')
            ->where('id_emp', getEmpresa())
            ->where('lixeira', false)
            ->orderby('quem_paga', 'DESC')
            ->orderby('descr')
            ->get();

        $salas = [];

        $modalidades = DB::table("procedimento")
            ->select('id', 'descr')
            ->where('procedimento.lixeira', "<>", 1)
            ->groupBy('id', 'descr')
            ->orderBy('descr')
            ->get();

        $old_modalidades = DB::table("old_modalidades")
            ->select('old_modalidades.id', 'old_modalidades.descr')
            ->join('old_atividades', 'old_atividades.id_modalidade', 'old_modalidades.id')
            //    ->join('old_mov_atividades', 'old_mov_atividades.id_atividade', 'old_atividades.id')

            ->where('old_modalidades.id_novo', 6)
            ->groupBy('old_modalidades.id', 'old_modalidades.descr')
            ->get();

        $agenda_status = DB::table('agenda_status')
            // ->where('id_emp', getEmpresa())
            ->where('lixeira', false)
            ->orderby('descr')
            ->get();

        $agenda_confirm = DB::table('agenda_confirmacao')
            // ->where('id_emp', getEmpresa())
            ->where('lixeira', false)
            ->orderby('descr')
            ->get();

        $tipo_agendamento = DB::table('tipo_procedimento')
            ->where('lixeira', false)
            // ->where('id_emp', getEmpresa())
            ->get();
        $contas_bancarias = DB::table('contas_bancarias')
            ->select('contas_bancarias.id', 'contas_bancarias.titular')
            ->where('id_emp', getEmpresaObj()->id)
            ->get();

        $agendamentos_ar = array();
        foreach ($profissionais as $profissional) {
            $grades = DB::table('grade')
                ->where('id_profissional', 28480)
                ->where('dia_semana', date('w') + 1)
                ->where('ativo', true)
                ->where('lixeira', 'N')
                ->get();
            $total_agendamentos = 0;
            foreach ($grades as $grade) {
                $total_agendamentos += ((intval(substr($grade->hora_final, 0, 2))) - (intval(substr($grade->hora_inicial, 0, 2)))) / ($grade->min_intervalo / 60);
            }
            $agendamentos = DB::table('agenda')
                ->selectRaw('COUNT(agenda.id) as total')
                ->where('id_profissional', $profissional->id)
                ->where('data', date('Y-m-d'))
                ->where('status', 'A')
                ->where('lixeira', false)
                ->value('total');
            if ($total_agendamentos == $agendamentos) {
                array_push($agendamentos_ar, 5);
            } else if ($agendamentos >= $total_agendamentos * 0.75) {
                array_push($agendamentos_ar, 4);
            } else if ($agendamentos >= $total_agendamentos * 0.5) {
                array_push($agendamentos_ar, 3);
            } else if ($agendamentos >= $total_agendamentos * 0.25) {
                array_push($agendamentos_ar, 2);
            } else if ($agendamentos >= 1) {
                array_push($agendamentos_ar, 1);
            } else
                array_push($agendamentos_ar, 0);
        }

        return view('new_agenda', compact('profissionais', 'fila_espera', 'convenios', 'salas', 'agenda_status', 'agenda_confirm', 'tipo_agendamento', 'agendamentos_ar', 'modalidades', 'old_modalidades', 'contas_bancarias'));
    }

    public function listar_agendamentos(Request $request)
    {
        try {
            $dia_semana = date('w', strtotime($request->date_selected));
            $data = new \StdClass;
            $data->profissional = DB::table('pessoa')
                ->select(
                    'pessoa.nome_fantasia AS nome',
                    'empresa.descr AS emp_descr'
                )
                ->join('empresa', 'empresa.id', 'pessoa.id_emp')
                ->where('pessoa.id', $request->id_profissional)
                ->first();

            $data->grade_bloqueios = DB::table('grade_bloqueio')
                ->where('grade_bloqueio.ativo', true)
                ->where('grade_bloqueio.id_profissional', $request->id_profissional)
                ->where('grade_bloqueio.dia_semana', ($dia_semana + 1))
                ->where('grade_bloqueio.data_inicial', '<=', $request->date_selected)
                ->where('grade_bloqueio.data_final', '>=', $request->date_selected)
                ->where('grade_bloqueio.id_emp', getEmpresa())
                // ->whereRaw("('" . $request->date_selected . "' BETWEEN grade_bloqueio.data_inicial AND grade_bloqueio.data_final)")
                ->get();
            $data->grades = DB::table('grade')
                ->select(
                    'etiqueta.cor',
                    'etiqueta.descr AS etiqueta_descr',
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade_horario.dia_semana',
                    'grade_horario.id',
                    'grade.max_qtde_pacientes AS max_qtde_pacientes'
                )
                ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
                ->leftjoin('etiqueta', 'etiqueta.id', 'grade.id_etiqueta')
                ->where('grade.id_profissional', $request->id_profissional)
                ->where('grade.ativo', true)
                ->where('grade.lixeira', 'N')
                ->where('grade.id_emp', getEmpresa())
                ->where('grade_horario.dia_semana', ($dia_semana + 1))
                ->whereRaw(
                    "     (grade.data_inicial <= '" . $request->date_selected . "'" .
                    " AND (grade.data_final IS NULL OR grade.data_final >= '" . $request->date_selected . "'))"
                )
                ->groupby(
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade_horario.dia_semana',
                    'grade_horario.id'
                )
                ->orderby('grade_horario.hora')
                ->orderby('grade_horario.dia_semana')
                ->orderby('grade_horario.id')
                ->get();
            $grades = DB::table('grade')
                ->select(
                    'etiqueta.cor',
                    'etiqueta.descr AS etiqueta_descr',
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade.max_qtde_pacientes',
                    'grade_horario.dia_semana',
                    'grade_horario.id'
                )
                ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
                ->leftjoin('etiqueta', 'etiqueta.id', 'grade.id_etiqueta')
                ->where('grade.id_profissional', $request->id_profissional)
                ->where('grade.ativo', true)
                ->where('grade.lixeira', 'N')
                ->whereRaw(
                    "     (grade.data_inicial <= '" . $request->date_selected . "'" .
                    " AND (grade.data_final IS NULL OR grade.data_final >= '" . $request->date_selected . "'))"
                )
                ->groupby(
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade_horario.dia_semana',
                    'grade_horario.id',
                    'etiqueta.cor',
                    'etiqueta.descr'
                )
                ->orderby('grade_horario.hora')
                ->orderby('grade_horario.dia_semana')
                ->orderby('grade_horario.id')
                ->get();
            $agendamentos = DB::table('agenda')
                ->select(
                    DB::raw("(select 0) AS sistema_antigo"),
                    'agenda.id',
                    'agenda.id_status',
                    'agenda.id_convenio',
                    'agenda.id_paciente',
                    'agenda.id_tipo_procedimento',
                    'agenda.id_confirmacao',
                    'agenda.status AS status',
                    'tipo_procedimento.descr AS tipo_procedimento',
                    'agenda.id_grade_horario',
                    'agenda.dia_semana',
                    'agenda.hora',
                    'pessoa.nome_fantasia AS nome_paciente',
                    'procedimento.descr AS descr_modalidade',
                    'procedimento.id    AS id_modalidade',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    'convenio.descr AS convenio_nome',
                    DB::raw(
                        " CASE " .
                        "    WHEN ((SELECT a2.id " .
                        "             FROM agenda AS a2 " .
                        '             LEFT OUTER JOIN agenda_status AS status ON status.id = a2.id_status' .
                        "            WHERE a2.id_paciente = agenda.id_paciente " .
                        "            ORDER BY data, hora" .
                        "            LIMIT 1) = agenda.id) THEN 1 " .
                        "    ELSE 0 " .
                        " END AS primeira_vez"
                    ),
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs")
                )
                ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                ->where('agenda.id_profissional', $request->id_profissional)
                ->where('agenda.data', $request->date_selected)
                ->where('agenda.lixeira', false)
                ->where('agenda.id_emp', getEmpresa())
                ->where(function ($sql) {
                    $sql->where("agenda.id_status", 6)
                        ->orWhere('agenda.id_status', 13)
                        ->orWhere('agenda.id_status', 7);
                });


            $agendamentos_old = DB::table('old_mov_atividades')
                ->select(
                    DB::raw("(select 1) AS sistema_antigo"),
                    'old_mov_atividades.id AS id',
                    DB::raw("CASE WHEN (old_mov_atividades.status = 'F') THEN (" .
                        "(select 13)" .
                        ") WHEN (old_mov_atividades.status = 'C') THEN (" .
                        "(select 16)" .
                        ") ELSE (select 6) END AS id_status"),
                    DB::raw("(select 0) AS id_convenio"),
                    'old_contratos.pessoas_id as id_paciente',
                    "old_mov_atividades.id_tipo_procedimento",
                    DB::raw('old_mov_atividades.id_confirmacao AS id_confirmacao'),
                    'old_mov_atividades.status AS status',
                    "tipo_procedimento.descr AS tipo_procedimento",
                    'old_mov_atividades.id_grade as id_grade_horario',
                    'old_mov_atividades.dia_semana AS dia_semana',
                    'old_mov_atividades.hora  as hora',
                    'pessoa.nome_fantasia AS nome_paciente',
                    'old_modalidades.descr AS descr_modalidade',
                    'old_modalidades.id_novo AS id_modalidade',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    DB::raw("(select '') as convenio_nome"),
                    DB::raw("(select 0) AS primeira_vez"),
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    DB::raw("(select 'sistema antigo') AS obs")
                )
                ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                ->leftjoin('old_plano_pagamento', 'old_plano_pagamento.id', 'old_finanreceber.id_planopagamento')
                ->where('old_mov_atividades.id_membro', $request->id_profissional)
                ->where('old_mov_atividades.data', $request->date_selected)
                ->where('old_mov_atividades.id_emp', getEmpresa())
                ->where(function ($sql) {
                    $sql->where('old_mov_atividades.lixeira', 0)
                        ->orWhere('old_mov_atividades.lixeira', null);
                })
                ->where(function ($sql) {
                    $sql->where("old_mov_atividades.id_status", 6)
                        ->orWhere("old_mov_atividades.id_status", 13)
                        ->orWhere("old_mov_atividades.id_status", 7);
                })
                ->orderby('old_mov_atividades.dia_semana')
                ->orderby('old_mov_atividades.hora')
                ->orderby('agenda_status.libera_horario', 'DESC')
                ->groupBy(
                    'old_mov_atividades.id',
                    'old_mov_atividades.id_status',
                    'old_contratos.pessoas_id',
                    "old_mov_atividades.id_tipo_procedimento",
                    'old_mov_atividades.status',
                    "tipo_procedimento.descr",
                    'old_mov_atividades.id_grade',
                    'old_mov_atividades.dia_semana',
                    'old_mov_atividades.hora',
                    'pessoa.nome_fantasia',
                    'old_modalidades.descr',
                    'pessoa.celular1',
                    'pessoa.telefone1',
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr',
                    'agenda_status.cor',
                    'agenda_status.cor_letra'
                )
                ->unionAll($agendamentos)
                ->get();
            $data->agendamentos = $agendamentos_old;

            $data->pacientes = DB::table('agenda')
                ->select(
                    'agenda.id_paciente AS id',
                    'agenda.id_emp'
                )
                ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
                ->join('grade', function ($join) use ($request, $dia_semana) {
                    $join->on('grade.id', 'grade_horario.id_grade')
                        ->where('grade.ativo', true)
                        ->where('grade.id_profissional', $request->id_profissional)
                        ->where('grade.dia_semana', ($dia_semana + 1))
                        ->whereRaw(
                            "     (grade.data_inicial <= '" . $request->date_selected . "'" .
                            " AND (grade.data_final IS NULL OR grade.data_final >= '" . $request->date_selected . "'))"
                        );
                })
                ->where('agenda.id_profissional', $request->id_profissional)
                ->where('agenda.data', '=', $request->date_selected)
                ->groupby(
                    'agenda.id_paciente',
                    'agenda.id_emp'
                )
                ->get();

            return json_encode($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function expandir_agendamento_view(Request $request)
    {
        $begin_week = new \DateTime($request->date_selected);
        $end_week = new \DateTime($request->date_selected);
        if ($begin_week->format('N') != 7)
            $begin_week = $begin_week->modify('Last Sunday');
        if ($end_week->format('N') != 6)
            $end_week = $end_week->modify('Next Saturday');

        $data = new \StdClass;

        $grades = DB::table('grade_horario')
            ->selectRaw('grade_horario.id, grade_horario.hora')
            ->join('grade', 'grade.id', 'grade_horario.id_grade')
            ->where('grade.id_profissional', $request->id_profissional)
            ->where('grade.ativo', true)
            ->where('grade.dia_semana', $request->dia_semana)
            ->orderBy('grade_horario.hora')
            ->get();


        $grades_cheias = DB::table('grade_horario')
            ->selectRaw('grade_horario.id,
                             grade.max_qtde_pacientes,
                             grade_horario.hora,
                             COUNT(agenda.id) as agendamentos')
            ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
            ->leftjoin('agenda', 'agenda.id_grade_horario', 'grade_horario.id')
            ->where('grade.id_profissional', $request->id_profissional)
            ->where('grade.ativo', true)
            ->where(function ($sql) use ($begin_week, $request) {
                $sql->where('agenda.data', date('Y-m-d', strtotime($begin_week->format('Y-m-d') . ' +' . ($request->dia_semana - 1) . ' days')))
                    ->orWhere('agenda.data', null);

            })
            ->where(function ($sql) {
                $sql->where('agenda.lixeira', false)
                    ->orWhere('agenda.lixeira', null);
            })
            ->where(function ($sql) {
                $sql->where('agenda.status', 'A')
                    ->orWhere('agenda.status', null);
            })
            ->groupBy(
                'grade_horario.id',
                'grade.max_qtde_pacientes',
                'grade_horario.hora'
            )

            ->get();
        $data->grades = $grades;
        $data->grades_cheias = $grades_cheias;
        return json_encode($data);
    }
    public function listar_todos_agendamentos_semana(Request $request)
    {
        $begin_week = new \DateTime($request->date_selected);
        $end_week = new \DateTime($request->date_selected);
        if ($begin_week->format('N') != 7)
            $begin_week = $begin_week->modify('Last Sunday');
        if ($end_week->format('N') != 6)
            $end_week = $end_week->modify('Next Saturday');

        $profissionais = DB::table('pessoa')
            ->select(
                "pessoa.id",
                "pessoa.nome_fantasia",
                DB::raw("SUM(CASE WHEN grade.max_qtde_pacientes IS NOT NULL THEN grade.max_qtde_pacientes ELSE 50 END) AS max_qtde_pacientes"),
                DB::raw("SUM(CASE WHEN agenda.status = 'A' AND agenda.data > '" . $begin_week->format('Y-m-d') . "' AND agenda.data < '" . $end_week->format('Y-m-d') . "' THEN '1' ELSE '' END) AS max_agendamentos")
            )
            ->join('pessoa', 'pessoa.id', 'grade.id_profissional')
            ->join('grade', 'grade.id_profissional', 'pessoa.id')
            ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
            ->where('pessoa.id_emp', getEmpresa())
            ->where(function ($sql) {
                $sql->where('colaborador', 'P')
                    ->orWhere('colaborador', 'A');
            })
            ->where('pessoa.lixeira', false)
            ->groupBy('pessoa.nome_fantasia', 'grade_horario.hora', 'grade.max_qtde_pacientes', 'grade.dia_semana')
            ->orderBy('nome_fantasia')
            ->get();
        return $profissionais;
        $grades = array();
        foreach ($profissionais as $profissional) {
            return DB::table('grade_horario')
                ->select(
                    'pessoa.nome_fantasia',
                    'grade_horario.hora',
                    DB::raw("SUM(CASE WHEN grade.max_qtde_pacientes IS NOT NULL THEN grade.max_qtde_pacientes ELSE 50 END) AS max_qtde_pacientes"),
                    DB::raw("SUM(CASE WHEN agenda.status = 'A' AND agenda.data > '" . $begin_week->format('Y-m-d') . "' AND agenda.data < '" . $end_week->format('Y-m-d') . "' THEN '1' ELSE '' END) AS max_agendamentos"),
                    'grade.dia_semana'
                )
                ->join('grade', 'grade.id', 'grade_horario.id_grade')
                ->join('pessoa', 'pessoa.id', 'grade.id_profissional')
                ->leftjoin('agenda', 'agenda.id_grade_horario', 'grade_horario.id')
                ->where('grade.id_profissional', $profissional->id)
                ->where('grade.ativo', true)
                // ->where('agenda.data', '>=', $begin_week)
                // ->where('agenda.data', '<=', $end_week)
                ->groupBy('pessoa.nome_fantasia', 'grade_horario.hora', 'grade.max_qtde_pacientes', 'grade.dia_semana')
                ->orderBy('grade.dia_semana')
                ->orderBy('grade_horario.hora')
                ->get();



        }
    }
    public function confirmar_agendamento_mobile(Request $request)
    {
        $agendamento = Agenda::find($request->id);
        $agendamento->id_confirmacao = $request->id_confirmacao;
        $agendamento->save();
        return $agendamento;
    }
    public function listar_todos_agendamentos_semanal(Request $request)
    {
        $begin_week = new \DateTime($request->date_selected);
        $end_week = new \DateTime($request->date_selected);
        if ($begin_week->format('N') != 7)
            $begin_week = $begin_week->modify('Last Sunday');
        if ($end_week->format('N') != 6)
            $end_week = $end_week->modify('Next Saturday');

        $dias = array();
        for ($i = 0; $i < 7; $i++) {
            array_push($dias, date("d", strtotime($begin_week->format("Y-m-d") . '+' . strval($i) . 'days')));
        }

        // $profissionais = DB::table('pessoa')
        //                 ->selectRaw("pessoa.id, pessoa.nome_fantasia")
        //                 ->where('id_emp', getEmpresa())
        //                 ->where(function($sql){
        //                     $sql->where('colaborador', 'P')
        //                         ->orWhere('colaborador','A');
        //                 })
        //                 ->where('lixeira', false)
        //                 ->orderBy('nome_fantasia')
        //                 ->get();
        $profissionais = DB::table('pessoa')
            ->select(DB::raw("pessoa.id, pessoa.nome_fantasia"))
            ->whereRaw("id_emp = " . getEmpresa() . " AND (colaborador = 'P' or colaborador = 'A') AND pessoa.lixeira = 0")->orderBy('pessoa.nome_fantasia')->get();
        // return $profissionais;

        $grade_existe = array();
        $grade_cheia = array();
        foreach ($profissionais as $profissional) {
            $grade_por_profissional = array();
            $grade_cheia_por_profissional = array();
            for ($i = 1; $i < 8; $i++) {
                $grade = DB::table('grade')
                    ->select(DB::raw("grade.max_qtde_pacientes"))
                    ->whereRaw("dia_semana = " . $i . " AND ativo = 1 AND (data_final is null or data_final > '" . date('Y-m-d') . "') AND lixeira = 'N' AND id_profissional = " . $profissional->id)->get();

                if (sizeof($grade) > 0) {
                    array_push($grade_por_profissional, 'S');
                    $total_agendamentos = 0;
                    foreach ($grade as $grade) {
                        if ($grade->max_qtde_pacientes > 0 && $grade->max_qtde_pacientes != null) {
                            $total_agendamentos += $grade->max_qtde_pacientes;
                        } else
                            $total_agendamentos = 500;
                    }
                    $agendamentos = DB::table('agenda')
                        ->selectRaw('COUNT(*) as total')
                        ->where('id_profissional', $profissional->id)
                        ->where('data', date("Y-m-d", strtotime($begin_week->format("Y-m-d") . '+' . strval($i - 1) . 'days')))
                        ->where('status', 'A')
                        ->where(function ($sql) {
                            $sql->where('lixeira', false)
                                ->orWhere('lixeira', null)
                                ->orWhere('lixeira', 0);
                        })
                        ->value('total');
                    $agendamentos_old = DB::table('old_mov_atividades')
                        ->selectRaw('COUNT(*) as total')
                        ->where('id_membro', $profissional->id)
                        ->where('old_mov_atividades.data', date("Y-m-d", strtotime($begin_week->format("Y-m-d") . '+' . strval($i - 1) . 'days')))
                        ->where('old_mov_atividades.status', "A")
                        ->where(function ($sql) {
                            $sql->where('lixeira', false)
                                ->orWhere('lixeira', null)
                                ->orWhere('lixeira', 0);
                        })
                        ->value('total');

                    array_push($grade_cheia_por_profissional, ((($agendamentos + $agendamentos_old) * 100) / $total_agendamentos));
                } else {
                    array_push($grade_por_profissional, 'N');
                    array_push($grade_cheia_por_profissional, 0);
                }

            }
            array_push($grade_existe, $grade_por_profissional);
            array_push($grade_cheia, $grade_cheia_por_profissional);
        }
        $data = new \StdClass;
        $data->grade_existe = $grade_existe;
        $data->profissionais = $profissionais;
        $data->grade_cheia = $grade_cheia;
        $data->dias = $dias;

        return json_encode($data);
    }

    public function listar_agendamentos_semanal(Request $request)
    {
        try {
            $begin_week = new \DateTime($request->date_selected);
            $end_week = new \DateTime($request->date_selected);
            if ($begin_week->format('N') != 7)
                $begin_week = $begin_week->modify('Last Sunday');
            if ($end_week->format('N') != 6)
                $end_week = $end_week->modify('Next Saturday');

            $grades = DB::table('grade')
                ->select(
                    'etiqueta.cor',
                    'etiqueta.descr AS etiqueta_descr',
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade.max_qtde_pacientes',
                    'grade_horario.dia_semana',
                    'grade_horario.id'
                )
                ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
                ->leftjoin('etiqueta', 'etiqueta.id', 'grade.id_etiqueta')
                ->where('grade.id_profissional', $request->id_profissional)
                ->where('grade.ativo', true)
                ->where('grade.lixeira', 'N')
                ->where('grade.id_emp', getEmpresa())
                // ->whereRaw(
                //     "    ((grade.data_inicial >= '" . $begin_week->format('Y-m-d') . "'" .
                //     " AND (grade.data_final IS NULL OR grade.data_final >= '" . $begin_week->format('Y-m-d') . "' AND
                //     grade.data_final <= '". $end_week->format('Y-m-d') . "'))" .
                //     "  OR (grade.data_inicial <= '" . $end_week->format('Y-m-d') . "'" .
                //     " AND (grade.data_final IS NULL OR grade.data_final >= '" . $end_week->format('Y-m-d') . "')))"
                ->whereRaw("(
                            (
                                (
                                    grade.data_inicial <= '" . $begin_week->format('Y-m-d') . "' OR
                                    grade.data_inicial <= DATE_ADD('" . $begin_week->format('Y-m-d') . "', interval grade.dia_semana day)
                                )
                                OR (grade.data_inicial is null)
                            )
                            AND
                            (
                                (
                                    grade.data_final >= '" . $begin_week->format('Y-m-d') . "' OR
                                    grade.data_final >= DATE_ADD('" . $begin_week->format('Y-m-d') . "', interval grade.dia_semana day)
                                )
                                OR (grade.data_final is null)
                            )
                        )")
                ->groupby(
                    'grade.min_intervalo',
                    'grade_horario.hora',
                    'grade_horario.dia_semana',
                    'grade_horario.id',
                    'etiqueta.cor',
                    'etiqueta.descr'
                )
                ->orderby('grade_horario.hora')
                ->orderby('grade_horario.dia_semana')
                ->orderby('grade_horario.id')
                ->get();

            $agendamentos = DB::table('agenda')
                ->select(
                    DB::raw("(select 0) AS sistema_antigo"),
                    'agenda.id',
                    'agenda.id_status',
                    'agenda.id_convenio',
                    'agenda.id_paciente',
                    'agenda.id_tipo_procedimento',
                    'agenda.id_confirmacao',
                    'agenda.status AS status',
                    'tipo_procedimento.descr AS tipo_procedimento',
                    'agenda.id_grade_horario',
                    'agenda.dia_semana',
                    'agenda.hora',
                    'pessoa.nome_fantasia AS nome_paciente',
                    'procedimento.descr AS descr_procedimento',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    'convenio.descr AS convenio_nome',
                    DB::raw(
                        " CASE " .
                        "    WHEN ((SELECT a2.id " .
                        "             FROM agenda AS a2 " .
                        '             LEFT OUTER JOIN agenda_status AS status ON status.id = a2.id_status' .
                        "            WHERE a2.id_paciente = agenda.id_paciente " .
                        "            ORDER BY data, hora" .
                        "            LIMIT 1) = agenda.id) THEN 1 " .
                        "    ELSE 0 " .
                        " END AS primeira_vez"
                    ),
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs")
                )
                ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                // ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
                // ->leftjoin('grade', function($join) use ($begin_week, $end_week) {
                //     $join->on('grade.id', 'grade_horario.id_grade')
                //     ->where('grade.ativo', true)
                //         ->whereRaw(
                //             "    ((grade.data_inicial >= '" . $begin_week->format('Y-m-d') . "'" .
                //             " AND (grade.data_final IS NULL OR grade.data_final >= '" . $begin_week->format('Y-m-d') . "'))" .
                //             "  OR (grade.data_inicial <= '" . $end_week->format('Y-m-d') . "'" .
                //             " AND (grade.data_final IS NULL OR grade.data_final >= '" . $end_week->format('Y-m-d') . "')))"
                //         );
                //     })
                ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                ->where('agenda.id_profissional', $request->id_profissional)
                ->where('agenda.data', '>=', $begin_week)
                ->where('agenda.data', '<=', $end_week)
                ->where('agenda.lixeira', false)
                ->where('agenda.id_emp', getEmpresa())
                ->where(function ($sql) {
                    $sql->where("agenda.id_status", 6)
                        ->orWhere('agenda.id_status', 13)
                        ->orWhere('agenda.id_status', 7);
                });


            $agendamentos_old = DB::table('old_mov_atividades')
                ->select(
                    DB::raw("(select 1) AS sistema_antigo"),
                    'old_mov_atividades.id AS id',
                    DB::raw("CASE WHEN (old_mov_atividades.status = 'F') THEN (" .
                        "(select 13)" .
                        ") WHEN (old_mov_atividades.status = 'C') THEN (" .
                        "(select 16)" .
                        ") ELSE (select 6) END AS id_status"),
                    DB::raw("(select 0) AS id_convenio"),
                    'old_contratos.pessoas_id as id_paciente',
                    "old_mov_atividades.id_tipo_procedimento",
                    DB::raw('(select 0) AS id_confirmacao'),
                    'old_mov_atividades.status AS status',
                    "tipo_procedimento.descr AS tipo_procedimento",
                    'old_mov_atividades.id_grade as id_grade_horario',
                    'old_mov_atividades.dia_semana AS dia_semana',
                    'old_mov_atividades.hora  as hora',
                    'pessoa.nome_fantasia AS nome_paciente',
                    'old_modalidades.descr AS descr_procedimento',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    DB::raw("(select '') as convenio_nome"),
                    DB::raw("(select 0) AS primeira_vez"),
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    DB::raw("(select 'sistema antigo') AS obs")
                )
                ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                ->leftjoin('old_plano_pagamento', 'old_plano_pagamento.id', 'old_finanreceber.id_planopagamento')
                ->where('old_mov_atividades.id_membro', $request->id_profissional)
                ->where('old_mov_atividades.data', '>=', $begin_week)
                ->where('old_mov_atividades.data', '<=', $end_week)
                ->where('old_mov_atividades.id_emp', getEmpresa())
                ->where(function ($sql) {
                    $sql->where('old_mov_atividades.lixeira', 0)
                        ->orWhere('old_mov_atividades.lixeira', null);
                })
                ->where(function ($sql) {
                    $sql->where("old_mov_atividades.id_status", 6)
                        ->orWhere("old_mov_atividades.id_status", 13)
                        ->orWhere("old_mov_atividades.id_status", 7);
                })
                ->orderby('old_mov_atividades.dia_semana')
                ->orderby('old_mov_atividades.hora')
                ->orderby('agenda_status.libera_horario', 'DESC')
                ->groupBy(
                    'old_mov_atividades.id',
                    'old_mov_atividades.id_status',
                    'old_contratos.pessoas_id',
                    "old_mov_atividades.id_tipo_procedimento",
                    'old_mov_atividades.status',
                    "tipo_procedimento.descr",
                    'old_mov_atividades.id_grade',
                    'old_mov_atividades.dia_semana',
                    'old_mov_atividades.hora',
                    'pessoa.nome_fantasia',
                    'old_modalidades.descr',
                    'pessoa.celular1',
                    'pessoa.telefone1',
                    'agenda_status.permite_editar',
                    'agenda_status.libera_horario',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.caso_reagendar',
                    'agenda_status.descr',
                    'agenda_status.cor',
                    'agenda_status.cor_letra'
                )
                ->unionAll($agendamentos)
                ->get();

            $grade_bloqueios = DB::table('grade_bloqueio')
                ->where('grade_bloqueio.ativo', true)
                ->where('grade_bloqueio.id_profissional', $request->id_profissional)
                ->where(function ($sql) use ($begin_week, $end_week) {
                    $sql->where('grade_bloqueio.data_inicial', '>=', $begin_week->format('Y-m-d'))
                        ->orWhere('grade_bloqueio.data_inicial', '<=', $end_week->format('Y-m-d'))
                        ->orWhere('grade_bloqueio.data_final', '>=', $begin_week->format('Y-m-d'))
                        ->orWhere('grade_bloqueio.data_final', '<=', $end_week->format('Y-m-d'));
                })

                // ->where(function($sql) use ($begin_week, $end_week) {
                //     $sql->whereBetween('grade_bloqueio.data_inicial', [$begin_week->format('Y-m-d'), $end_week->format('Y-m-d')])
                //         ->orWhereBetween('grade_bloqueio.data_final', [$begin_week->format('Y-m-d'), $end_week->format('Y-m-d')]);
                // })
                ->where('id_emp', getEmpresa())
                ->get();



            $data = new \StdClass;
            $data->begin_week = $begin_week;
            $data->end_week = $end_week;
            $data->grades = $grades;
            $data->grade_bloqueios = $grade_bloqueios;
            $data->agendamentos = $agendamentos_old;
            $data->profissional = DB::table('pessoa')
                ->select(
                    'pessoa.nome_fantasia AS nome',
                    'empresa.descr AS emp_descr'
                )
                ->join('empresa', 'empresa.id', 'pessoa.id_emp')
                ->where('pessoa.id', $request->id_profissional)
                ->first();



            return json_encode($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function finalizar_agendamento(Request $request)
    {
        try {
            $agendamento = Agenda::find($request->id);
            $agendamento->status = 'F';
            $agendamento->id_status = statusCasoConfirmar()->id;
            $agendamento->updated_by = Auth::user()->name;
            $agendamento->updated_at = date('Y-m-d H:i:s');
            $agendamento->save();


            return 10;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function salvarLote(Request $request) {
        $agendamentos = array();
        for ($i = 0; $i < sizeof($request->sessoes); $i++) {
            $agendamento = new Agenda;

            $agendamento->id_pedido = $request->contrato;
            $agendamento->id_tabela_preco = $request->plano;
            $agendamento->id_paciente = $request->paciente;

            $agendamento->id_emp = $request->sessoes[$i]["empresa"];
            $agendamento->id_profissional = $request->sessoes[$i]["profissional"];
            $agendamento->id_grade_horario = $request->sessoes[$i]["grade_horario"];
            $agendamento->id_modalidade = $request->sessoes[$i]["modalidade"];
            $agendamento->data = $request->sessoes[$i]["data"];
            $agendamento->hora = $request->sessoes[$i]["hora"];
            $agendamento->dia_semana = $request->sessoes[$i]["dia_semana"];

            $agendamento->id_convenio = 0;
            $agendamento->id_confirmacao = 0;
            $agendamento->id_reagendado = 0;
            $agendamento->motivo_cancelamento = 0;
            $agendamento->id_tipo_procedimento = 1;
            $agendamento->id_status = 6;
            $agendamento->bordero = true;
            $agendamento->lixeira = false;
            $agendamento->reagendamento = false;
            $agendamento->status = 'A';
            $agendamento->obs = '';
            $agendamento->obs_cancelamento = '';

            $agendamento->created_by = Auth::user()->name;
            $agendamento->updated_by = Auth::user()->name;
            $agendamento->save();

            $obj = new \StdClass();
            $obj->data = $agendamento->data;
            $obj->dia_semana = $agendamento->dia_semana;
            $obj->hora = $agendamento->hora;
            $obj->descr_plano = TabelaPrecos::find($agendamento->id_tabela_preco)->descr;
            $obj->descr_associado = Pessoa::find($agendamento->id_paciente)->nome_fantasia;
            $obj->descr_profissional = Pessoa::find($agendamento->id_profissional)->nome_fantasia;
            array_push($agendamentos, $obj);
        }
        return json_encode($agendamentos);
    }

    function cancelar_agendamento(Request $request)
    {
        try {
            $agendamento = Agenda::find($request->id);
            $agendamento->status = 'C';
            $agendamento->id_status = statusCasoCancelar()->id;
            $agendamento->updated_by = Auth::user()->name;
            $agendamento->updated_at = date('Y-m-d H:i:s');
            $agendamento->motivo_cancelamento = $request->motivo;
            $agendamento->obs_cancelamento = $request->observacao;
            $agendamento->save();

            return redirect('/agenda');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    function mostrar_agendamento($id)
    {
        $agendamento = Agenda::find($id);
        if (TipoProcedimento::find($agendamento->id_tipo_procedimento)->assossiar_contrato == true) {
            return json_encode(
                DB::table('agenda')
                    ->select(
                        'pessoa.nome_fantasia                       AS descr_paciente',
                        'pessoa.id                                 AS id_paciente',
                        'pessoa.celular1                           AS celular',
                        'pessoa.telefone1                          AS telefone',
                        'pessoa.email                              AS email',
                        'tipo_procedimento.descr                   AS descr_tipo_procedimento',
                        'tipo_procedimento.assossiar_contrato      AS associar_contrato',
                        'tipo_procedimento.assossiar_especialidade AS associar_procedimento',
                        'agenda.id_tipo_procedimento               AS id_tipo_procedimento',
                        //  'convenio.descr                            AS descr_convenio',
                        //  'convenio.id                               AS id_convenio',
                        'agenda.data                               AS data',
                        'agenda.hora                               AS hora',
                        'agenda_status.descr                       AS descr_agenda_status',
                        'agenda_status.id                          AS id_agenda_status',
                        'agenda.obs                                AS obs',
                        'pedido.id                                 AS id_pedido',
                        'pedido.data                               AS data_pedido',
                        'tabela_precos.descr                       AS descr_tabela_precos',
                        'tabela_precos.id                          AS id_tabela_precos'
                    )
                    //  procedimento.descr          AS descr_procedimento,
                    //  procedimento.id             AS id_procedimento')
                    ->join('pessoa', 'pessoa.id', 'agenda.id_paciente', 'left outer join')
                    ->join('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento', 'left outer join')
                    // ->join('convenio',          'convenio.id',                 'agend    a.id_convenio',     'left outer join')
                    ->join('agenda_status', 'agenda_status.id', 'agenda.id_status', 'left outer join')
                    // ->join('procedimento',      'procedimento.id',             'agenda.id_procedimento', 'left outer join')
                    ->join('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco', 'left outer join')
                    ->join('pedido', 'pedido.id', 'agenda.id_pedido', 'left outer join')
                    ->where('agenda.id', $agendamento->id)
                    ->where('agenda.lixeira', false)
                    ->get()
            );
        } else if (TipoProcedimento::find($agendamento->id_tipo_procedimento)->assossiar_especialidade) {
            return json_encode(
                DB::table('agenda')
                    ->select(
                        'pessoa.nome_fantasia                      AS descr_paciente',
                        'pessoa.id                                 AS id_paciente',
                        'pessoa.celular1                           AS celular',
                        'pessoa.telefone1                          AS telefone',
                        'pessoa.email                              AS email',
                        'tipo_procedimento.descr                   AS descr_tipo_procedimento',
                        'tipo_procedimento.assossiar_contrato      AS associar_contrato',
                        'tipo_procedimento.assossiar_especialidade AS associar_procedimento',
                        'agenda.id_tipo_procedimento               AS id_tipo_procedimento',
                        // 'convenio.descr                            AS descr_convenio',
                        // 'convenio.id                               AS id_convenio',
                        'agenda.data                               AS data',
                        'agenda.hora                               AS hora',
                        'agenda_status.descr                       AS descr_agenda_status',
                        'agenda_status.id                          AS id_agenda_status',
                        'agenda.obs                                AS obs',
                        'procedimento.descr                        AS descr_procedimento',
                        'procedimento.id                           AS id_procedimento'
                    )
                    ->join('pessoa', 'pessoa.id', 'agenda.id_paciente', 'left outer join')
                    ->join('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento', 'left outer join')
                    // ->join('convenio',          'convenio.id',                 'agenda.id_convenio',          'left outer join')
                    ->join('agenda_status', 'agenda_status.id', 'agenda.id_status', 'left outer join')
                    ->join('procedimento', 'procedimento.id', 'agenda.id_modalidade', 'left outer join')
                    // ->join('tabela_precos',     'tabela_precos.id',            'agenda.id_tabela_preco', 'left outer join')
                    // ->join('pedido',            'pedido.id',                   'agenda.id_pedido',       'left outer join')
                    ->where('agenda.id', $agendamento->id)
                    ->where('agenda.lixeira', false)
                    ->get()
            );
        }
    }

    function agendamento_info($id)
    {
        try {
            return json_encode(
                DB::table('agenda')
                    ->select(
                        'agenda.id',
                        'agenda.id_paciente',
                        'agenda.id_grade_horario',
                        'agenda.id_convenio',
                        'agenda.id_confirmacao',
                        'agenda.id_status',
                        'agenda.id_pedido',
                        'agenda.id_tabela_preco',
                        'agenda.data',
                        'agenda.hora',
                        'pessoa.email',
                        'pessoa.nome_fantasia AS paciente_nome',
                        'pessoa.celular1 AS celular',
                        'pessoa.telefone1 AS telefone',
                        'profissional.nome_fantasia AS profissional_nome',
                        'procedimento.id    AS id_procedimento',
                        'procedimento.descr AS descr_procedimento',
                        'agenda.status',
                        'agenda.obs',
                        'agenda.id_tipo_procedimento',
                        'tipo_procedimento.descr AS tipo_procedimento',
                        'tipo_procedimento.assossiar_contrato AS assossiar_contrato',
                        'tipo_procedimento.assossiar_especialidade As assosciar_especialidade',
                        'agenda_confirmacao.descr AS descr_confirmacao',
                        'pedido.data AS data_contrato',
                        'pedido.id   AS id_contrato',
                        'tabela_precos.descr AS descr_plano',
                        'planos.id    AS id_plano',
                        'planos.descr        AS descricao'
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                    ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                    ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                    ->leftjoin('pedido', 'pedido.id', 'pedido.id', 'agenda.id_pedido')
                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                    ->leftjoin('tabela_precos as planos', 'planos.id', 'agenda.id_tabela_preco')
                    ->where('agenda.id', $id)
                    ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    function editar_agendamento($id)
    {
        try {
            $data = new \StdClass;
            $agendamento = DB::table('agenda')
                ->select(
                    "agenda.id_grade_horario     AS id_grade_horario",
                    "agenda.id_profissional      AS id_profissional",
                    "agenda.id                   AS id",
                    "pessoa.nome_fantasia        AS paciente_nome",
                    "agenda.id_paciente          AS id_paciente",
                    "agenda.id_tipo_procedimento AS id_tipo_procedimento",
                    "agenda.id_convenio          AS id_convenio",
                    "agenda.id_modalidade        AS id_modalidade",
                    "agenda.data                 AS data",
                    "agenda.hora                 AS hora",
                    "agenda.obs                  AS obs",
                    "agenda.id_pedido            AS id_contrato",
                    "agenda.id_tabela_preco      AS id_plano",
                    "agenda.id_tabela_preco      AS id_plano_pre",
                    "agenda.id_encaminhamento"
                )
                ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                ->where('agenda.id', $id)
                ->first();
            $data->agendamento = $agendamento;
            return json_encode($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function salvar_op_bordero(Request $request)
    {
        $agenda = Agenda::find($request->id_agendamento);
        if (strval($request->bordero === 'true')) {
            $agenda->bordero = 0;
        } else
            $agenda->bordero = 1;
        $encaminhamento = DB::table('agenda')
            ->select('agenda.id_encaminhamento')
            ->leftjoin('encaminhamento_detalhes', 'encaminhamento_detalhes.id_encaminhamento', 'agenda.id_encaminhamento');
        $agenda->save();
        return json_encode($agenda);
    }
    public function verificar_grade(Request $request)
    {
        try {
            $dia_semana = date('w', strtotime(str_replace("/", "-", $request->data)));
            $agenda = DB::table('grade_horario')
                ->select(
                    'grade_horario.id',
                    DB::raw("(select 0) as min_intervalo")
                )
                ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                ->where('grade.id_profissional', $request->id_profissional)
                ->where('grade_horario.dia_semana', ($dia_semana + 1))
                ->where('grade_horario.hora', $request->hora . ':00')
                ->where('grade.ativo', 1)
                ->orderby('grade_horario.created_at', 'DESC')
                ->first();
            $bloqueio = DB::table('grade_bloqueio')
                ->where('grade_bloqueio.ativo', true)
                ->where('grade_bloqueio.id_profissional', $request->id_profissional)
                ->where('grade_bloqueio.dia_semana', ($dia_semana + 1))
                ->whereRaw(
                    "('" . date('w', strtotime(str_replace("/", "-", $request->date_selected))) .
                    "' BETWEEN grade_bloqueio.data_inicial AND grade_bloqueio.data_final)"
                )
                ->whereRaw(
                    "('" . $request->hora . ":00' BETWEEN grade_bloqueio.hora_inicial AND grade_bloqueio.hora_final)"
                )
                ->count();


            $data = new \StdClass;
            $data->dia_semana = $dia_semana + 1;
            $data->grade_exist = ($agenda != null && $bloqueio == 0);
            if ($data->grade_exist) {
                $grade = DB::table('grade_horario')
                    ->select(
                        'grade.id',
                        'grade.min_intervalo',
                        'grade_horario.hora',
                        'grade_horario.dia_semana'
                    )
                    ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                    ->where('grade_horario.id', $agenda->id)
                    ->first();

                $horario_disponivel = true;
                // $qtde_intervalo = round($request->tempo_procedimento / $grade->min_intervalo, 0, PHP_ROUND_HALF_DOWN);
                // $zero_date = new \DateTime(date('Y-m-d') . ' ' . $grade->hora);
                // for ($i = 1; $i < $qtde_intervalo; $i++) {
                //     $zero_date->add(new \DateInterval('PT' . $grade->min_intervalo . 'M'));
                //     $grade_frente = DB::table('grade_horario')
                //     ->select(
                //         'grade.min_intervalo',
                //         'grade_horario.hora',
                //         'grade_horario.dia_semana',
                //         DB::raw('(SELECT COUNT(*) FROM agenda WHERE agenda.id_grade_horario = grade_horario.id) AS qtde_agenda')
                //         )
                //         ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                //         ->where('grade_horario.dia_semana', $grade->dia_semana)
                //         ->where('grade_horario.hora', $zero_date->format('H:i:s'))
                //         ->where('grade.id', $grade->id)
                //         ->first();

                //         if ($grade_frente == null || $grade_frente->qtde_agenda > 0) {
                //             $i = $qtde_intervalo;
                //             $horario_disponivel = false;
                //         }
                // }

                $data->id_grade_horario = $agenda->id;
                $data->horario_disponivel = $horario_disponivel;
                $data->tempo_excedido = ($request->tempo_procedimento > $agenda->min_intervalo);
            }
            return json_encode($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pesquisar_agendamento(Request $request)
    {
        try {
            $agendamentos = DB::table('agenda')
                ->select(
                    'agenda.id',
                    'agenda.id_emp',
                    'agenda.id_status',
                    'agenda.status',
                    'agenda.id_tipo_procedimento',
                    'tipo_procedimento.descr AS tipo_procedimento',
                    'agenda.id_grade_horario',
                    'grade_horario.dia_semana',
                    'agenda.hora',
                    'agenda.data',
                    'agenda.id_paciente',
                    'pessoa.nome_fantasia AS nome_paciente',
                    DB::raw(
                        '(CASE' .
                        '   WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido ' .
                        '   ELSE profissional.nome_fantasia ' .
                        'END) AS nome_profissional'
                    ),
                    'procedimento.descr AS descr_procedimento',
                    'fila_espera.status AS espera',
                    'fila_espera.hora_chegada',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    'convenio.descr AS convenio_nome',
                    DB::raw(
                        " CASE " .
                        "    WHEN ((SELECT a2.id " .
                        "             FROM agenda AS a2 " .
                        '             LEFT OUTER JOIN agenda_status AS status ON status.id = a2.id_status' .
                        "            WHERE a2.id_paciente = agenda.id_paciente " .
                        '              AND status.libera_horario = 0' .
                        "            ORDER BY data, hora" .
                        "            LIMIT 1) = agenda.id) THEN 1 " .
                        "    ELSE 0 " .
                        " END AS primeira_vez"
                    ),
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
                    'agenda.obs',
                    DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs")
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
                ->where('agenda.id_emp', getEmpresa())
                ->where('pessoa.nome_fantasia', 'LIKE', '%' . $request->search . '%')
                ->where('agenda.lixeira', false)
                ->orderby('agenda.data', 'DESC')
                ->orderby('agenda.hora')
                ->get();

            return json_encode($agendamentos);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mudar_status(Request $request)
    {

        $agenda = Agenda::find($request->id_agendamento);
        $agenda->id_status = $request->status;

        if ($request->status == 13)
            $agenda->status = 'F';
        else if ($request->status == 16)
            $agenda->status = 'C';
        else
            $agenda->status = 'A';
        $agenda->save();

        return 'true';
    }

    function checar_cadastro_completo($id_pessoa)
    {
        try {
            $data = new \StdClass;
            $data->msg = '';
            $data->completo = true;
            $pessoa = DB::table('pessoa')
                ->where('id', $id_pessoa)
                ->first();

            if ($pessoa->nome_fantasia == '' || $pessoa->nome_fantasia == null) {
                $data->msg .= 'Nome; ';
                $data->completo = false;
            }
            // if ($pessoa->email         == '' || $pessoa->email         == null) {
            //     $data->msg .= 'Email; ';
            //     $data->completo = false;
            // }
            if ($pessoa->sexo == '' || $pessoa->sexo == null) {
                $data->msg .= 'Sexo; ';
                $data->completo = false;
            }
            if ($pessoa->cpf_cnpj == '' || $pessoa->cpf_cnpj == null) {
                $data->msg .= 'CPF; ';
                $data->completo = false;
            }
            if ($pessoa->data_nasc == '' || $pessoa->data_nasc == null) {
                $data->msg .= 'Data de Nascimento; ';
                $data->completo = false;
            }
            if (
                $pessoa->endereco == '' || $pessoa->endereco == null ||
                $pessoa->numero == '' || $pessoa->numero == null ||
                $pessoa->bairro == '' || $pessoa->bairro == null ||
                $pessoa->cidade == '' || $pessoa->cidade == null ||
                $pessoa->uf == '' || $pessoa->uf == null
            ) {
                $data->msg .= 'Endereço;';
                $data->completo = false;
            }
            return $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mudar_tipo_confirmacao(Request $request)
    {
        try {
            $agenda = Agenda::find($request->id_agendamento);
            $agenda->id_confirmacao = $request->contato;
            $agenda->save();

            $historico = new HistoricoAgenda;
            $historico->id_emp = getEmpresa();
            $historico->id_agenda = $agenda->id;
            $historico->id_status = $agenda->id_status;
            $historico->id_tipo_procedimento = $agenda->id_tipo_procedimento;
            $historico->id_procedimento = $agenda->id_modalidade;
            $historico->id_tipo_confirmacao = $agenda->id_confirmacao;
            $historico->campo = 'agenda_confirmacao';
            $historico->created_by = Auth::user()->name;
            $historico->save();

            return $agenda->id;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function copiar_agendamento(Request $request)
    {
        try {
            $agendamento_clip = Agenda::find($request->id_agendamento);

            $dia_semana = date('w', strtotime($request->dia));
            $agenda = DB::table('grade_horario')
                ->select('grade_horario.id')
                ->join('grade', 'grade.id', 'grade_horario.id_grade')
                ->where('grade.id_profissional', $agendamento_clip->id_profissional)
                ->where('grade_horario.dia_semana', ($dia_semana + 1))
                ->where('grade_horario.hora', $request->hora)
                ->orderby('grade_horario.created_at', 'DESC')
                ->first();

            $bloqueio = DB::table('grade_bloqueio')
                ->where('grade_bloqueio.ativo', true)
                ->where('grade_bloqueio.id_profissional', $agendamento_clip->id_profissional)
                ->where('grade_bloqueio.dia_semana', ($dia_semana + 1))
                ->whereRaw(
                    "('" . date('w', strtotime($request->dia)) . "' BETWEEN grade_bloqueio.data_inicial AND grade_bloqueio.data_final)"
                )
                ->whereRaw(
                    "('" . $request->hora . "' BETWEEN grade_bloqueio.hora_inicial AND grade_bloqueio.hora_final)"
                )
                ->count();

            if ($agenda != null && $bloqueio == 0) {
                $agendamento_new = new Agenda;

                $agendamento_new->id_emp = $agendamento_clip->id_emp;
                $agendamento_new->id_grade_horario = $agenda->id;

                $agendamento_new->id_profissional = $agendamento_clip->id_profissional;
                $agendamento_new->id_paciente = $agendamento_clip->id_paciente;
                $agendamento_new->id_modalidade = $agendamento_clip->id_modalidade;
                $agendamento_new->id_convenio = $agendamento_clip->id_convenio;
                $agendamento_new->id_status = $agendamento_clip->id_status;
                $agendamento_new->id_confirmacao = $agendamento_clip->id_confirmacao;

                $agendamento_new->data = $request->dia;
                $agendamento_new->hora = $request->hora;

                $agendamento_new->id_tipo_procedimento = $agendamento_clip->id_tipo_procedimento;
                $agendamento_new->obs = $agendamento_clip->obs;
                $agendamento_new->convenio = $agendamento_clip->convenio;
                $agendamento_new->status = $agendamento_clip->status;
                $agendamento_new->created_by = Auth::user()->name;
                $agendamento_new->updated_by = Auth::user()->name;
                $agendamento_new->save();

                return $agendamento_new;
            } else {
                return $agendamento_clip;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function copiar_agendamento_id(Request $request)
    {
        try {
            $agendamento_clip = Agenda::find($request->id_agendamento_clipboard);

            $agendamento_new = new Agenda;
            $agendamento_new->id_emp = $agendamento_clip->id_emp;
            $agendamento_new->id_grade_horario = $agendamento_clip->id_grade_horario;
            $agendamento_new->id_profissional = $agendamento_clip->id_profissional;
            $agendamento_new->id_paciente = $agendamento_clip->id_paciente;
            $agendamento_new->id_modalidade = $agendamento_clip->id_modalidade;
            $agendamento_new->id_convenio = $agendamento_clip->id_convenio;
            $agendamento_new->id_status = $agendamento_clip->id_status;
            $agendamento_new->id_confirmacao = $agendamento_clip->id_confirmacao;

            $agendamento_new->data = DB::table('agenda')->where('id', $request->id_agendamento)->value('data');
            $agendamento_new->hora = DB::table('agenda')->where('id', $request->id_agendamento)->value('hora');

            $agendamento_new->id_tipo_procedimento = $agendamento_clip->id_tipo_procedimento;
            $agendamento_new->obs = $agendamento_clip->obs;
            $agendamento_new->convenio = $agendamento_clip->convenio;
            $agendamento_new->status = $agendamento_clip->status;
            $agendamento_new->created_by = Auth::user()->name;
            $agendamento_new->updated_by = Auth::user()->name;
            $agendamento_new->save();

            return $agendamento_new;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request)
    {
        try {
            // $agendamento = Agenda::find(133233);
            // $agendamento->lixeira = 1;
            // $agendamento->save();

            $agendamento = DB::table('agenda')
                ->updateOrInsert([
                    "id" => $request->id
                ], [
                        "lixeira" => 1
                    ]);
            return 'true';
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    public function agendamentosPorPessoa($id_pessoa)
    {
        try {
            $agendamentos = DB::table('agenda')
                ->select(
                    'agenda.id',
                    'agenda.id_emp',
                    'agenda.id_status',
                    'agenda.status',
                    'agenda.id_tipo_procedimento',
                    'tipo_procedimento.descr AS tipo_procedimento',
                    'agenda.id_grade_horario',
                    'grade_horario.dia_semana',
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
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    'convenio.descr AS convenio_nome',
                    DB::raw(
                        " CASE " .
                        "    WHEN ((SELECT a2.id " .
                        "             FROM agenda AS a2 " .
                        '             LEFT OUTER JOIN agenda_status AS status ON status.id = a2.id_status' .
                        "            WHERE a2.id_paciente = agenda.id_paciente " .
                        '              AND status.libera_horario = 0' .
                        "            ORDER BY data, hora" .
                        "            LIMIT 1) = agenda.id) THEN 1 " .
                        "    ELSE 0 " .
                        " END AS primeira_vez"
                    ),
                    'agenda_status.permite_editar',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    'grade.min_intervalo',
                    'agenda.id_confirmacao',
                    'agenda_confirmacao.descr AS descr_confirmacao',
                    'agenda.obs'
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
                // ->where('agenda.id_emp', getEmpresa())
                ->where('pessoa.id', $id_pessoa)
                ->where(function ($sql) {
                    $sql->where('agenda.lixeira', false)
                        ->orWhere('agenda.lixeira', null);
                })
                ->orderBy('agenda.data', 'DESC');
            $agendamentos_old = DB::table('old_mov_atividades')
                ->select(
                    'old_mov_atividades.id AS id',
                    DB::raw('(select 1) AS id_emp'),
                    DB::raw("CASE WHEN (old_mov_atividades.status = 'F') THEN (" .
                        "(select 13)" .
                        ") WHEN (old_mov_atividades.status = 'C') THEN (" .
                        "(select 16)" .
                        ") ELSE (select 6) END AS id_status"),
                    'old_mov_atividades.status AS status',
                    "old_mov_atividades.id_tipo_procedimento",
                    "tipo_procedimento.descr AS tipo_procedimento",
                    'old_mov_atividades.id_grade as id_grade_horario',
                    'grade.dia_semana AS dia_semana',
                    'grade_horario.hora as hora',
                    'old_mov_atividades.data as data',
                    'old_contratos.pessoas_id as id_paciente',
                    'profissional.id as id_profissional',
                    'pessoa.nome_fantasia AS nome_paciente',
                    'profissional.nome_fantasia AS nome_profissional',
                    'old_modalidades.descr AS descr_procedimento',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    DB::raw('GROUP_CONCAT(DISTINCT old_plano_pagamento.descr) as convenio_nome'),
                    DB::raw('(select 0) AS primeira_vez'),
                    'agenda_status.permite_editar',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.descr AS descr_status',
                    'agenda_status.cor AS cor_status',
                    'agenda_status.cor_letra',
                    'grade.min_intervalo',
                    DB::raw('(select 0) AS id_confirmacao'),
                    DB::raw("(select '') AS descr_confirmacao"),
                    DB::raw("(select 'sistema antigo') AS obs")
                )

                ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                ->leftjoin('grade_horario', 'grade_horario.id', 'old_mov_atividades.id_grade')
                ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                ->leftjoin('old_plano_pagamento', 'old_plano_pagamento.id', 'old_finanreceber.id_planopagamento')

                ->where('old_contratos.pessoas_id', $id_pessoa)
                ->where(function ($sql) {
                    $sql->where('old_mov_atividades.lixeira', null)
                        ->orWhere('old_mov_atividades.lixeira', false);
                })
                ->groupBy(
                    'old_mov_atividades.id',
                    'old_mov_atividades.status',
                    "old_mov_atividades.id_tipo_procedimento",
                    "tipo_procedimento.descr",
                    'old_mov_atividades.id_grade',
                    'grade.dia_semana',
                    'grade_horario.hora',
                    'old_mov_atividades.data',
                    'old_contratos.pessoas_id',
                    'profissional.id',
                    'pessoa.nome_fantasia',
                    'profissional.nome_fantasia',
                    'old_modalidades.descr',
                    'pessoa.celular1',
                    'pessoa.telefone1',
                    'agenda_status.permite_editar',
                    'agenda_status.permite_fila_espera',
                    'agenda_status.permite_reagendar',
                    'agenda_status.descr',
                    'agenda_status.cor',
                    'agenda_status.cor_letra',
                    'grade.min_intervalo'
                )
                ->union($agendamentos)
                ->orderBy('data', 'DESC')
                ->orderBy('hora', 'DESC')
                ->get();
            return json_encode($agendamentos_old);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function validar_agendamento(Request $request)
    {
        try {
            $pedido = DB::table("pedido")
                ->where("id_paciente", $request->paciente_id)
                ->get();
            return $pedido;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    public function faturar($id_agendamento)
    {
        return json_encode(DB::table('agenda')
            ->select(
                'pessoa.id AS id_pessoa',
                'pessoa.nome_fantasia AS descr_pessoa',
                'tipo_procedimento.consulta AS consulta',
                'tipo_procedimento.assossiar_especialidade AS associar_especialidade',
                'agenda.id_convenio AS id_convenio',
                'agenda.id_encaminhamento As id_encaminhamento',
                'convenio.descr     AS descr_convenio',
                'agenda.obs AS obs'
            )
            ->leftjoin('pessoa', 'pessoa.id', 'id_paciente')
            ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
            ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
            ->where('agenda.id', $id_agendamento)
            ->where('agenda.lixeira', false)
            ->first());
    }
    public function salvar_faturamento(Request $request)
    {
        try {

            $agenda = Agenda::find($request->id);
            $agenda->id_emp = getEmpresa();
            $agenda->id_status = statusCasoConfirmar()->id;
            $agenda->status = 'F';
            $agenda->save();

            $confirmacao = new AgendaConfirmados;
            $confirmacao->id_emp = getEmpresa();
            $confirmacao->id_agenda = $agenda->id;
            $confirmacao->id_tipo_agendamento = $agenda->id_tipo_procedimento;
            $confirmacao->created_by = Auth::user()->id_profissional;
            $confirmacao->save();
            $total = 0;
            $val_aux = 0;

            foreach ($request->formas_pag as $forma_pag) {
                $forma_pag = (object) $forma_pag;
                $val_aux += $forma_pag->forma_pag_valor;
            }
            $confirmacao->valor_total = $val_aux;
            $confirmacao->save();
            DB::table('pedido_parcela')
                ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id', 'pedido_parcela.id_pedido_forma_pag')
                ->where('pedido_forma_pag.id_agenda_confirmacao', $confirmacao->id)
                ->delete();
            DB::table('pedido_forma_pag')->where('id_agenda_confirmacao', $confirmacao->id)->delete();

            foreach ($request->formas_pag as $forma_pag) {
                $forma_pag = (object) $forma_pag;

                $pedido_forma_pag = new PedidoFormaPag;
                $pedido_forma_pag->id_emp = getEmpresa();
                $pedido_forma_pag->id_agenda_confirmacao = $confirmacao->id;
                $pedido_forma_pag->id_forma_pag = $forma_pag->id_forma_pag;
                $pedido_forma_pag->id_financeira = $forma_pag->id_financeira;
                $pedido_forma_pag->num_total_parcela = $forma_pag->parcela;
                $pedido_forma_pag->valor_total = $forma_pag->forma_pag_valor;
                $pedido_forma_pag->tipo = $request->tipo_forma_pag;
                $pedido_forma_pag->save();

                $valor_parcela = $forma_pag->forma_pag_valor / $forma_pag->parcela;
                if ($valor_parcela * $forma_pag->parcela < $forma_pag->forma_pag_valor) {
                    $acrescimo = $forma_pag->forma_pag_valor - ($valor_parcela * $forma_pag->parcela);
                } else {
                    $acrescimo = 0;
                }

                for ($i = 0; $i < $forma_pag->parcela; $i++) {
                    $pedido_parcela = new PedidoParcela;
                    $pedido_parcela->id_emp = getEmpresa();
                    $pedido_parcela->id_pedido_forma_pag = $pedido_forma_pag->id;
                    $pedido_parcela->parcela = ($i + 1);
                    if ($acrescimo > 0 && $i == 0)
                        $pedido_parcela->valor = $valor_parcela + $acrescimo;
                    else
                        $pedido_parcela->valor = $valor_parcela;
                    if ($i == 0) {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento)));
                    } else {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento))) . ' + ' . (($i + 1) * 30) . ' days'));
                    }
                    $pedido_parcela->save();
                }
            }
            return json_encode('10');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function validar_plano_semana(Request $request)
    {
        try {
            $total = DB::table('pedido_planos')
                ->select(DB::raw("(pedido_planos.qtd_total * pedido_planos.qtde) AS total"))
                ->where('pedido_planos.id_pedido', $request->id_pedido)
                ->where('pedido_planos.id_plano', $request->id_tabela_preco)
                ->groupBy('pedido_planos.qtd_total', 'pedido_planos.qtde')
                ->value('total');
            $consumidas = DB::table('agenda')
                ->where('id_pedido', $request->id_pedido)
                ->where('id_tabela_preco', $request->id_tabela_preco)
                ->where(function ($sql) {
                    $sql->where("agenda.status", 'F')
                        ->orWhere("agenda.status", "A");
                })
                ->where(function ($sql) {
                    $sql->where('agenda.lixeira', false)
                        ->orWhere('agenda.lixeira', null);
                })
                ->count();
            if ($consumidas < $total) {
                return 'true';
            } else
                return 'false';

        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
    public function modal_agendamento_lote($paciente, $contrato, $plano)
    {
        $data = new \StdClass;

        $data->associado = Pessoa::find($paciente);
        $data->contrato = Pedido::find($contrato);
        $data->plano = TabelaPrecos::find($plano);

        /*        $data->agenda    = Agenda::find($id);
        $data->contrato  = Pedido::find($data->agenda->id_pedido);
        $data->plano     = TabelaPrecos::find($data->agenda->id_tabela_preco);
        $data->associado = Pessoa::find($data->agenda->id_paciente);
        $data->data      = new \DateTime($data->agenda->data);
        $data->data = $data->data->format('d/m/Y');*/

        return json_encode($data);

    }

    public function listar_atividades_semanais($id)
    {
        $agendamentos = DB::table('agenda')
            ->select(
                'agenda.id_tabela_preco as id_tabela_preco',
                'agenda.id_modalidade as id_procedimento',
                'tabela_precos.descr as descr_plano',
                'procedimento.descr as descr_modalidade'
            )
            ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
            ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
            ->where('agenda.status', 'F')
            ->where('agenda.lixeira', 0)
            ->where('agenda.id_paciente', $id)
            ->where('agenda.data', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-1 months')))
            ->unionAll(
                DB::table('old_mov_atividades')
                    ->select(
                        'old_mov_atividades.id_atividade as id_tabela_preco',
                        'procedimento.id as id_procedimento',
                        'old_modalidades.descr as descr_plano',
                        'procedimento.descr as descr_modalidade'
                    )
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('procedimento', 'procedimento.id', 'old_modalidades.id_novo')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->where('old_contratos.pessoas_id', $id)
                    ->where('old_mov_atividades.status', 'F')
                    ->where('old_mov_atividades.lixeira', '0')
                    ->where('old_mov_atividades.data', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-1 months')))
            )
            ->get();

        return json_encode($agendamentos);
    }

    public function listar_profissionais_lote(Request $request)
    {
        $agenda = DB::table('agenda')
            //   ->select('grade.dia_semana')
            ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
            ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
            ->where('agenda.id', $request->id_agendamento)
            ->first();
        // $agenda = Agenda::find($request->id_agendamento);
        $contrato = Pedido::find($agenda->id_pedido);
        $plano = TabelaPrecos::find($agenda->id_tabela_preco);
        $data_inicial = Agenda::find($request->id_agendamento)->data;
        if ($request->data_final = 0)
            $data_final = date('Y-m-d', strtotime($data_inicial . ' +' . $plano->vigencia . ' days'));
        else
            $data_final = new \DateTime(strtotime($request->data_final));
        $profissionais_ativos = array();
        $profissionais_ar = array();
        $profissionais = DB::table('pessoa')
            ->select(
                'pessoa.id                     AS id_profissional',
                'pessoa.nome_fantasia          AS descr_profissional'
            )
            ->leftjoin('grade', 'grade.id', 'grade.id_profissional')
            ->where(function ($sql) {
                $sql->where('pessoa.colaborador', 'P')
                    ->orWhere('pessoa.colaborador', 'A');
            })
            ->where('pessoa.lixeira', false)
            ->get();
        foreach ($profissionais as $profissional) {
            $control = 0;

            $grade_horario = DB::table('grade_horario')
                ->selectRaw('grade_horario.id,
                                      grade.max_qtde_pacientes,
                                      grade_horario.dia_semana,
                                      grade.id_profissional')
                ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                ->where('grade_horario.hora', $request->hora)
                ->where('grade.id_profissional', $profissional->id_profissional)
                ->where('grade_horario.dia_semana', $request->dia_semana)
                ->get();
            if (sizeof($grade_horario) == 0)
                $control = 1;
            foreach ($grade_horario as $grade) {
                $agendaaux = DB::table('agenda')
                    ->selectRaw('COUNT(data) AS agendamentos,
                                        data as data')
                    ->where('data', '>=', $data_inicial)
                    ->where('data', '<=', $data_final)
                    ->where('id_profissional', $agenda->id_profissional)
                    ->where('hora', $request->hora)
                    ->where('id_grade_horario', $grade->id)
                    ->where(function ($sql) {
                        $sql->where('agenda.status', 'F')
                            ->orWhere('agenda.status', 'A');
                    })
                    ->where('agenda.lixeira', false)
                    ->groupBy('data')
                    ->get();
                // return $agendaaux;
                if (sizeof($agendaaux) > 0) {
                    foreach ($agendaaux as $ag) {
                        if ($ag->agendamentos === $grade->max_qtde_pacientes) {
                            $control = 1;
                        }

                    }
                }
            }

            if ($control == 0)
                array_push($profissionais_ativos, 'S');
            else
                array_push($profissionais_ativos, 'N');
            array_push($profissionais_ar, $profissional);
        }
        $data = new \StdClass;
        $data->profissionais = $profissionais_ar;
        $data->profissionais_ativos = $profissionais_ativos;
        return json_encode($data);
    }
    public function expandir_agendamento($id, $sistema_antigo)
    {
        if ($sistema_antigo == '0') {
            if (Agenda::find($id)->id_tipo_procedimento == 1) {
                return json_encode(DB::table('agenda')
                    ->select(
                        'pessoa.nome_fantasia                      AS descr_pessoa',
                        'tipo_procedimento.descr                   AS descr_tipo_procedimento',
                        'tipo_procedimento.assossiar_contrato       AS associar_contrato',
                        'tipo_procedimento.assossiar_especialidade  AS associar_procedimento',
                        'procedimento.descr                         AS descr_procedimento',
                        'agenda.created_at                         AS created_at',
                        'pedido.data_validade                      AS validade_contrato',
                        "tabela_precos.descr                       AS descr_tabela_precos",
                        "agenda.id_tipo_procedimento"
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                    ->leftjoin('pedido', 'pedido.id', 'agenda.id_pedido')
                    ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                    ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                    ->where('agenda.id', $id)
                    ->where('agenda.lixeira', false)
                    ->first());
            } else {
                return json_encode(DB::table('agenda')
                    ->select(
                        'pessoa.nome_fantasia                      AS descr_pessoa',
                        DB::raw("CASE WHEN (agenda.id_tipo_procedimento = 3) THEN (" .
                            "procedimento.descr" .
                            ") ELSE procedimento.descr END AS descr_procedimento"),
                        'tipo_procedimento.descr                   AS descr_tipo_procedimento',
                        'tipo_procedimento.assossiar_contrato      AS associar_contrato',
                        'tipo_procedimento.assossiar_especialidade AS associar_procedimento',
                        'agenda.created_at                         AS created_at',
                        "agenda.id_tipo_procedimento"
                    )
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                    ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                    ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                    ->where('agenda.id', $id)
                    ->first());
            }
        } else {
            return json_encode(DB::table('old_mov_atividades')
                ->select(
                    'pessoa.nome_fantasia      AS descr_pessoa',
                    "old_modalidades.descr     AS descr_procedimento",
                    'tipo_procedimento.descr   AS descr_tipo_procedimento',
                    DB::raw("CONCAT(old_mov_atividades.dt_criado, ' ', old_mov_atividades.hr_criado) AS created_at")
                )

                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                ->where("old_mov_atividades.id", $id)
                ->first());
        }


        // if ($agendamento->assossiar_especialidade == true){
        //         return json_encode(DB::table('agenda')
        //                 ->select('pessoa.nome_fantasia                      AS descr_pessoa',
        //                         DB::raw("CASE WHEN (agenda.obs = 'sistema antigo') THEN (".
        //                                     "(select procedimento.descr)".
        //                                 ") ELSE (select tabela_precos.descr) END as descr_procedimento"),
        //                         'tipo_procedimento.descr                   AS descr_tipo_procedimento',
        //                         'tipo_procedimento.assossiar_contrato       AS associar_contrato',
        //                         'tipo_procedimento.assossiar_especialidade  AS associar_procedimento',
        //                         'agenda.created_at                         AS created_at')
        //                 ->leftjoin('tipo_procedimento',   'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
        //                 ->leftjoin('procedimento',        'procedimento.id',      'agenda.id_procedimento',      'left outer join')
        //                 ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_procedimento')
        //                 ->leftjoin('pessoa',              'pessoa.id',            'agenda.id_paciente',          'left outer join')
        //                 ->where('agenda.id', $id)
        //                 ->first());
        // }
        // else if ($agendamento->assossiar_contrato == true) {
        //     return json_encode(DB::table('agenda')
        //             ->select('pessoa.nome_fantasia                      AS descr_pessoa',
        //                      'tipo_procedimento.descr                   AS descr_tipo_procedimento',
        //                      'tipo_procedimento.assossiar_contrato       AS associar_contrato',
        //                      'tipo_procedimento.assossiar_especialidade  AS associar_procedimento',
        //                      'agenda.created_at                         AS created_at',
        //                      'pedido.data_validade                      AS validade_contrato',
        //                      'tabela_precos.descr                       AS descr_tabela_precos'
        //                      )
        //             ->join('tipo_procedimento',   'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
        //             ->join('pedido',              'pedido.id',            'agenda.id_pedido')
        //             ->join('tabela_precos',       'tabela_precos.id',     'agenda.id_tabela_preco',      'left outer join')
        //             ->join('pessoa',              'pessoa.id',            'agenda.id_paciente',          'left outer join')
        //             ->where('agenda.id', $id)
        //             ->where('agenda.lixeira', false)
        //             ->first());
        // }
        // else return json_encode(DB::table('agenda')
        //                                 ->select('pessoa.nome_fantasia                      AS descr_pessoa',
        //                                         'tipo_procedimento.descr                    AS descr_tipo_procedimento',
        //                                         'tipo_procedimento.assossiar_contrato       AS associar_contrato',
        //                                         'tipo_procedimento.assossiar_especialidade  AS associar_procedimento',
        //                                         'agenda.created_at                         AS created_at',
        //                                         'procedimento.descr                        AS descr_procedimento')
        //                                 ->join('tipo_procedimento',   'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
        //                                 ->join('procedimento',        'procedimento.id',      'agenda.id_procedimento',      'left outer join')
        //                                 ->join('pessoa',              'pessoa.id',            'agenda.id_paciente',          'left outer join')
        //                                 ->where('agenda.id', $id)
        //                                 ->first());
    }
    public function gerar_agendamentos_em_lote(Request $request)
    {
        try {
            $agendamento_base = Agenda::find($request->id);
            $contrato = Pedido::find($agendamento_base->id_pedido);
            $plano = TabelaPrecos::find($agendamento_base->id_tabela_preco);
            $data_inicial = $request->dinicial;
            $agendados = 0;
            $feriados = 0;
            $agendamentos_ar = array();
            $teste = str_replace('/', '-', $request->dfinal);
            $teste = new \DateTime($teste);

            if ($request->dfinal != 0)
                $data_final = $teste->format('Y-m-d');
            else
                $data_final = date('Y-m-d', strtotime($data_inicial . ' +' . $plano->vigencia . 'days'));

            for ($i = 0; $i < sizeof($request->dias_semana); $i++) {
                $data = $agendamento_base->data;

                $id_grade_hr = DB::table('grade_horario')
                    ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                    ->where('grade.id_profissional', $request->membros_id[$i])
                    ->where('grade.dia_semana', $request->dias_semana[$i])
                    ->where('grade_horario.hora', $request->horarios[$i])
                    ->where('ativo', true)
                    ->value('grade_horario.id');
                while (strtotime($data) <= strtotime($data_final) and (($agendados < $plano->max_atv) / sizeof($request->horarios))) {
                    if (sizeof(DB::table('feriados')->where('dia', $data)->get()) > 0) {
                        $feriados++;
                    } else {
                        $agenda = new Agenda;

                        $agenda->id_grade_horario = $id_grade_hr;
                        $agenda->id_profissional = $request->membros_id[$i];
                        $agenda->id_paciente = $agendamento_base->id_paciente;
                        $agenda->id_modalidade = $agendamento_base->id_modalidade;
                        $agenda->id_tipo_procedimento = $agendamento_base->id_tipo_procedimento;
                        $agenda->id_pedido = $agendamento_base->id_pedido;
                        $agenda->id_tabela_preco = $agendamento_base->id_tabela_preco;
                        $agenda->id_convenio = $agendamento_base->id_convenio;
                        $agenda->id_status = $agendamento_base->id_status;
                        $agenda->id_confirmacao = $agendamento_base->id_confirmacao;
                        $agenda->data = $data;
                        $agenda->hora = $request->horarios[$i];
                        $agenda->obs = $agendamento_base->obs;
                        $agenda->reagendamento = $agendamento_base->reagendamento;
                        $agenda->id_reagendado = $agendamento_base->id_reagendado;
                        $agenda->convenio = $agendamento_base->convenio;
                        $agenda->status = $agendamento_base->status;
                        $agenda->created_by = Auth::user()->name;
                        $agenda->updated_by = Auth::user()->name;
                        $agenda->motivo_cancelamento = $agendamento_base->motivo_cancelamento;
                        $agenda->obs_cancelamento = $agendamento_base->obs_cancelamento;
                        $agenda->save();
                        $agendados++;
                        array_push($agendamentos_ar, DB::table('agenda')
                            ->select(
                                'membro.nome_fantasia     AS descr_profissional',
                                'associado.nome_fantasia  AS descr_associado',
                                'agenda.data              AS data',
                                'agenda.hora              AS hora',
                                'tabela_precos.descr      AS descr_plano',
                                'grade_horario.dia_semana AS dia_semana'
                            )
                            ->leftjoin('pessoa AS membro', 'membro.id', 'agenda.id_profissional')
                            ->leftjoin('pessoa AS associado', 'associado.id', 'agenda.id_paciente')
                            ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
                            ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                            ->where('agenda.id', $agenda->id)
                            ->where('agenda.lixeira', false)
                            ->first());
                    }
                    $data = date("Y-m-d", strtotime($data . ' +1 week'));
                }

            }
            $agendamento_base->delete();
            $data = new \StdClass;
            $data->agendados = $agendados;
            $data->feriados = $feriados;
            $data->agendamentos = $agendamentos_ar;
            return json_encode($data);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function unificarTP($tabela) {
        $resultado = array();
        $ids = array();
        foreach($tabela as $linha) {
            if (!in_array($linha->id, $ids)) {
                array_push($ids, $linha->id);
                array_push($resultado, $linha);
            }
        }
        return $resultado;
    }

    public function listar_planos_desc($id_pessoa, $id_convenio)
    {
        $empresa = getEmpresa();
        $regra_associado = DB::table('associados_regra')
            ->where('ativo', true)
            ->value('dias_pos_fim_contrato');

        $associado = DB::table('pedido')
            ->select('pedido.data_validade')
            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
            ->where('id_paciente', $id_pessoa)
            ->where('pedido.lixeira', 0)
            ->where('pedido.tipo_contrato', '<>', 'P')
            ->where('pedido.status', 'F')
            ->where('tabela_precos.associado', 'S')
            ->where('pedido.data_validade', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
            ->orderBy('pedido.data_validade', 'DESC')
            ->unionAll(
                DB::table('old_contratos')
                    ->select('datafinal AS data_validade')
                    ->where('old_contratos.pessoas_id', $id_pessoa)
                    ->where('situacao', '1')
                    ->where('old_contratos.datafinal', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
                    ->orderBy('datafinal', 'DESC')
            )
            ->first();


        $data = new \StdClass;
        $data->tabela_precos = DB::table('tabela_precos')
            ->select(
                'tabela_precos.descr AS descr_tabela_preco',
                'tabela_precos.id                  AS id',
                'tabela_precos.vigencia            AS vigencia',
                'tabela_precos.n_pessoas           AS n_pessoas',
                DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" .
                    "CONCAT(tabela_precos.descr,' | mensal'))" .
                    "WHEN (tabela_precos.vigencia = 60) THEN (" .
                    "CONCAT(tabela_precos.descr, ' | bimestral'))" .
                    "WHEN (tabela_precos.vigencia = 90) THEN (" .
                    "CONCAT(tabela_precos.descr, ' | trimestral'))" .
                    "WHEN (tabela_precos.vigencia = 180) THEN (" .
                    "CONCAT(tabela_precos.descr, ' | semestral'))" .
                    "WHEN (tabela_precos.vigencia = 360) THEN (" .
                    "CONCAT(tabela_precos.descr, ' | anual'))" .
                    " ELSE '' END AS descr"),

                'tabela_precos.desconto_associados AS desconto_associados',
                'tabela_precos.valor               AS valor',
                'preco_convenios_plano.valor       AS valor_convenio'
            )
            ->leftjoin('preco_convenios_plano', 'preco_convenios_plano.id_tabela_preco', 'tabela_precos.id')
            ->leftjoin('empresas_plano', 'empresas_plano.id_tabela_preco', 'tabela_precos.id')
            ->where('empresas_planos.id_emp', getEmpresa())
            //  ->where('tabela_precos.id_emp', getEmpresa())
            ->where('tabela_precos.pre_agendamento', true)
            ->where(function ($sql) use ($id_convenio) {
                $sql->where('preco_convenios_plano.id_convenio', $id_convenio)
                    ->orWhere("preco_convenios_plano.id_convenio", null);
            })
            ->where(function ($sql) {
                $sql->where('preco_convenios_plano.lixeira', 0)
                    ->orWhere('preco_convenios_plano.lixeira', null);
            })
            //  ->where(function($sql) use($empresa){
            //     if ($empresa == 2) {
            //         $sql->whereRaw("(tabela_precos.descr not like '%habilitacao%')");
            //     }
            // })
            ->where('tabela_precos.lixeira', 0)
            ->get();
        $data->tabela_precos = $this->unificarTP($data->tabela_precos);
        if (!$associado) {
            $data->associado = 'N';
        } else {
            if (date($associado->data_validade, strtotime('+' . $regra_associado . 'days')) > date('Y-m-d')) {
                $data->lista = array();
                $data->associado = 'S';
                foreach ($data->tabela_precos as $plano) {
                    if ($plano->desconto_associados == null)
                        array_push($data->lista, 'N');
                    else
                        array_push($data->lista, 'S');
                }
            } else
                $data->associado = 'N';
        }
        if ($id_convenio === '' or $id_convenio === null or $id_convenio === 0) {
            $data->convenio = 'N';
        } else {
            $data->valores_conv = array();
            $data->convenio = 'S';
            foreach ($data->tabela_precos as $plano) {
                if ($plano->valor_convenio == null)
                    array_push($data->valores_conv, 'N');
                else
                    array_push($data->valores_conv, 'S');
            }
        }
        return json_encode($data);

    }

    public function listar_planos_desc2($id_pessoa, $id_convenio, $id_profissional)
    {
        $empresa = getEmpresa();
        $regra_associado = DB::table('associados_regra')
            ->where('ativo', true)
            ->value('dias_pos_fim_contrato');

        $associado = DB::table('pedido')
            ->select('pedido.data_validade')
            ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
            ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
            ->where('tabela_precos.associado', 'S')
            ->where('pedido.id_paciente', $id_pessoa)
            ->where('pedido.lixeira', 0)
            ->where('pedido.tipo_contrato', '<>', 'P')
            ->where('pedido.status', 'F')
            ->where('pedido.data_validade', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
            ->orderBy('pedido.data_validade', 'DESC')
            ->unionAll(
                DB::table('old_contratos')
                    ->select('datafinal AS data_validade')
                    ->where('old_contratos.pessoas_id', $id_pessoa)
                    ->where('situacao', '1')
                    ->where('old_contratos.datafinal', '>=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . $regra_associado . 'days')))
                    ->orderBy('datafinal', 'DESC')
            )
            ->first();


        $data = new \StdClass;
        if ($id_convenio == null || $id_convenio == 'null' || $id_convenio == '0' || $id_convenio == 0 || $id_convenio == 'undefined') {
            $data->tabela_precos = DB::table('tabela_precos')
                ->select(
                    'tabela_precos.descr AS descr_tabela_preco',
                    'tabela_precos.id                  AS id',
                    'tabela_precos.vigencia            AS vigencia',
                    'tabela_precos.n_pessoas           AS n_pessoas',
                    DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" .
                        "CONCAT(tabela_precos.descr,' | mensal'))" .
                        "WHEN (tabela_precos.vigencia = 60) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | bimestral'))" .
                        "WHEN (tabela_precos.vigencia = 90) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | trimestral'))" .
                        "WHEN (tabela_precos.vigencia = 180) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | semestral'))" .
                        "WHEN (tabela_precos.vigencia = 360) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | anual'))" .
                        " ELSE '' END AS descr"),

                    'tabela_precos.desconto_associados AS desconto_associados',
                    'tabela_precos.valor               AS valor',
                    DB::raw("(select '') AS valor_convenio")
                )
                ->leftjoin('preco_convenios_plano', 'preco_convenios_plano.id_tabela_preco', 'tabela_precos.id')
                ->leftjoin('modalidades_por_plano', 'modalidades_por_plano.id_tabela_preco', 'tabela_precos.id')
                ->leftjoin('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                ->leftjoin('especialidade_pessoa', 'especialidade_pessoa.id_especialidade', 'especialidade.id')
                ->leftjoin('empresas_plano', 'empresas_plano.id_tabela_preco', 'tabela_precos.id')
                ->where('empresas_plano.id_emp', getEmpresa())
                //  ->where('tabela_precos.pre_agendamento', true)
                //  ->where(function($sql) use ($id_convenio){  
                //      $sql->where('preco_convenios_plano.id_convenio', $id_convenio)
                //          ->orWhere("preco_convenios_plano.id_convenio", null)
                //          ->orWhere("preco_convenios_plano.id_convenio", 'null')
                //          ->orWhere("preco_convenios_plano.id_convenio", '');
                //  })

                //  ->where(function($sql) use($empresa){
                //     if ($empresa == 2) {
                //         $sql->whereRaw("(tabela_precos.descr not like '%habilitacao%')");
                //     }
                // })
                ->where('tabela_precos.lixeira', 0)
                ->where('tabela_precos.max_atv', 1)
                ->groupBy(
                    'tabela_precos.descr',
                    'tabela_precos.id',
                    'tabela_precos.vigencia',
                    'tabela_precos.n_pessoas',
                    'tabela_precos.desconto_associados',
                    'tabela_precos.valor'
                )
                ->get();

        } else {
            $data->tabela_precos = DB::table('tabela_precos')
                ->select(
                    'tabela_precos.descr AS descr_tabela_preco',
                    'tabela_precos.id                  AS id',
                    'tabela_precos.vigencia            AS vigencia',
                    'tabela_precos.n_pessoas           AS n_pessoas',
                    DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" .
                        "CONCAT(tabela_precos.descr,' | mensal'))" .
                        "WHEN (tabela_precos.vigencia = 60) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | bimestral'))" .
                        "WHEN (tabela_precos.vigencia = 90) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | trimestral'))" .
                        "WHEN (tabela_precos.vigencia = 180) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | semestral'))" .
                        "WHEN (tabela_precos.vigencia = 360) THEN (" .
                        "CONCAT(tabela_precos.descr, ' | anual'))" .
                        " ELSE '' END AS descr"),

                    'tabela_precos.desconto_associados AS desconto_associados',
                    'tabela_precos.valor               AS valor',
                    'preco_convenios_plano.valor       AS valor_convenio'
                )
                ->leftjoin('preco_convenios_plano', 'preco_convenios_plano.id_tabela_preco', 'tabela_precos.id')
                ->leftjoin('modalidades_por_plano', 'modalidades_por_plano.id_tabela_preco', 'tabela_precos.id')
                ->leftjoin('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                ->leftjoin('especialidade_pessoa', 'especialidade_pessoa.id_especialidade', 'especialidade.id')
                ->leftjoin('empresas_plano', 'empresas_plano.id_tabela_preco', 'tabela_precos.id')
                ->where('empresas_plano.id_emp', getEmpresa())
                //  ->where('tabela_precos.id_emp', getEmpresa())
                //  ->where(function($sql) use($empresa){
                //     if ($empresa == 2){
                //         $sql->whereRaw("(tabela_precos.descr NOT LIKE '%HABILITAÇÃO%')");
                //     }
                //  })
                //  ->where('tabela_precos.pre_agendamento', true)
                //  ->where(function($sql) use ($id_convenio){  
                //     $sql->where('preco_convenios_plano.id_convenio', $id_convenio)
                //         ->orWhere("preco_convenios_plano.id_convenio", null)
                //         ->orWhere("preco_convenios_plano.id_convenio", 'null')
                //         ->orWhere("preco_convenios_plano.id_convenio", '');
                // })
                ->where('preco_convenios_plano.id_convenio', $id_convenio)
                ->where(function ($sql) {
                    $sql->where('preco_convenios_plano.lixeira', 0)
                        ->orWhere('preco_convenios_plano.lixeira', null);
                })
                //  ->where(function($sql) use($empresa){
                //     if ($empresa == 2) {
                //         $sql->whereRaw("(tabela_precos.descr not like '%habilitacao%')");
                //     }
                // })
                ->where('tabela_precos.lixeira', 0)
                //  ->where('especialidade_pessoa.id_profissional', $id_profissional)
                ->groupBy(
                    'tabela_precos.descr',
                    'tabela_precos.id',
                    'tabela_precos.vigencia',
                    'tabela_precos.n_pessoas',
                    'tabela_precos.desconto_associados',
                    'tabela_precos.valor',
                    'preco_convenios_plano.valor'
                )
                ->get();
        }
        $data->tabela_precos = $this->unificarTP($data->tabela_precos);
        if (!$associado) {
            $data->associado = 'N';
        } else {
            if (date($associado->data_validade, strtotime('+' . $regra_associado . 'days')) > date('Y-m-d')) {
                $data->lista = array();
                $data->associado = 'S';
                foreach ($data->tabela_precos as $plano) {
                    if ($plano->desconto_associados == null)
                        array_push($data->lista, 'N');
                    else
                        array_push($data->lista, 'S');
                }
            } else
                $data->associado = 'N';
        }
        if ($id_convenio === 'null' || $id_convenio == null || $id_convenio === 0 || $id_convenio === '0' || $id_convenio == 'undefined') {
            $data->convenio = 'N';
        } else {
            $data->valores_conv = array();
            $data->convenio = 'S';
            foreach ($data->tabela_precos as $plano) {
                if ($plano->valor_convenio == null)
                    array_push($data->valores_conv, 'N');
                else
                    array_push($data->valores_conv, 'S');
            }
        }
        return json_encode($data);
    }

    public function listar_agendamentos_pendentes(Request $request)
    {
        $data_inicial = new \DateTime($request->data_inicial);
        $data_final = new \DateTime($request->data_final);

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
            ->where('agenda.id_emp', getEmpresa())
            ->where('agenda.lixeira', 0)
            ->where('agenda.data', '>=', $data_inicial->format('Y-m-d'))
            ->where('agenda.data', '<=', $data_final->format('Y-m-d'))
            ->where(function ($sql) use ($request) {
                if ($request->id_membro != 0) {
                    $sql->where('agenda.id_profissional', $request->id_membro);
                }
            })
            ->where(function ($sql) use ($request) {
                if ($request->completo_incompleto != 'A') {
                    switch ($request->completo_incompleto) {

                        case 'C':
                            if ($request->status == 'F') {
                                $sql->Where('agenda.id_modalidade', '<>', 0)
                                    ->Where('agenda.id_tabela_preco', '<>', 0)
                                    ->Where('agenda.id_pedido', '<>', 0);
                            } else {
                                $sql->Where('agenda.id_modalidade', '<>', 0)
                                    ->Where('agenda.id_tabela_preco', '<>', 0);
                            }
                            break;
                        case 'I':
                            if ($request->status == 'F') {
                                $sql->orWhere('agenda.id_modalidade', 0)
                                    ->orWhere('agenda.id_tabela_preco', 0)
                                    ->orWhere('agenda.id_pedido', 0);
                            } else {
                                $sql->orWhere('agenda.id_modalidade', 0)
                                    ->orWhere('agenda.id_tabela_preco', 0);
                            }


                    }
                }
            })
            ->where('id_tipo_procedimento', '<>', 5)
            ->where('agenda.status', $request->status)
            ->unionAll(
                DB::table('old_mov_atividades')
                    ->select(
                        "old_mov_atividades.id",
                        DB::raw("(select 1) AS id_emp"),
                        "old_mov_atividades.id_status",
                        "old_mov_atividades.status",
                        "old_mov_atividades.id_tipo_procedimento",
                        "tipo_procedimento.descr AS tipo_procedimento",
                        "old_mov_atividades.id_grade AS id_grade_horario",
                        "old_mov_atividades.hora",
                        "old_mov_atividades.data",
                        "old_contratos.pessoas_id AS id_paciente",
                        "pessoa.nome_fantasia AS nome_paciente",
                        "profissional.nome_fantasia AS nome_profissional",
                        "old_modalidades.descr AS descr_procedimento",
                        DB::raw("(select '') AS convenio_nome"),
                        "agenda_status.permite_editar",
                        "agenda_status.libera_horario",
                        "agenda_status.permite_fila_espera",
                        "agenda_status.permite_reagendar",
                        "agenda_status.descr AS descr_status",
                        "agenda_status.cor AS cor_status",
                        "agenda_status.cor_letra",
                        DB::raw("(select 0) AS min_intervalo"),
                        DB::raw("(select 0) AS id_confirmacao"),
                        DB::raw("(select '') AS descr_confirmacao"),
                        DB::raw("(select '') AS obs"), DB::raw("(select 1) AS antigo")
                    )
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    // ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_atividades.id_contrato')
                    // ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_financeira')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                    ->where('old_mov_atividades.lixeira', 0)
                    ->where('old_mov_atividades.data', '>=', $data_inicial->format('Y-m-d'))
                    ->where('old_mov_atividades.data', '<=', $data_final->format('Y-m-d'))
                    ->where(function ($sql) use ($request) {
                        if ($request->id_membro != 0) {
                            $sql->where('old_mov_atividades.id_membro', $request->id_membro);
                        }
                    })
                    ->where(function ($sql) use ($request) {
                        if ($request->completo_incompleto == 'I') {
                            $sql->where('old_mov_atividades.id_pedido', 0)
                                ->where('old_mov_atividades.id_tipo_procedimento', 4);
                        }
                    })
                    ->where('old_mov_atividades.status', $request->status)
                    ->groupBy(
                        "old_mov_atividades.id",
                        "old_mov_atividades.id_status",
                        "old_mov_atividades.status",
                        "old_mov_atividades.id_tipo_procedimento",
                        "tipo_procedimento.descr",
                        "old_mov_atividades.id_grade",
                        "old_mov_atividades.hora",
                        "old_mov_atividades.data",
                        "old_contratos.pessoas_id",
                        "pessoa.nome_fantasia",
                        "profissional.nome_fantasia",
                        "old_modalidades.descr",
                        "agenda_status.permite_editar",
                        "agenda_status.libera_horario",
                        "agenda_status.permite_fila_espera",
                        "agenda_status.permite_reagendar",
                        "agenda_status.descr",
                        "agenda_status.cor",
                        "agenda_status.cor_letra"
                    )
            )
            ->orderby('data', 'DESC')
            ->orderby('hora')
            ->get();

        return json_encode($agendamentos);
    }

    public function imprimir($data_inicial, $data_final, $id_membro, $status, $completo_incompleto)
    {

        $data_inicial = new \DateTime($data_inicial);
        $data_final = new \DateTime($data_final);

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
            ->where('agenda.id_emp', getEmpresa())
            ->where('agenda.lixeira', 0)
            ->where('agenda.data', '>=', $data_inicial->format('Y-m-d'))
            ->where('agenda.data', '<=', $data_final->format('Y-m-d'))
            ->where(function ($sql) use ($id_membro) {
                if ($id_membro != 0) {
                    $sql->where('agenda.id_profissional', $id_membro);
                }
            })
            ->where(function ($sql) use ($completo_incompleto, $status) {
                if ($completo_incompleto != 'A') {
                    switch ($completo_incompleto) {

                        case 'C':
                            if ($status == 'F') {
                                $sql->Where('agenda.id_modalidade', '<>', 0)
                                    ->Where('agenda.id_tabela_preco', '<>', 0)
                                    ->Where('agenda.id_pedido', '<>', 0);
                            } else {
                                $sql->Where('agenda.id_modalidade', '<>', 0)
                                    ->Where('agenda.id_tabela_preco', '<>', 0);
                            }
                            break;
                        case 'I':
                            if ($status == 'F') {
                                $sql->orWhere('agenda.id_modalidade', 0)
                                    ->orWhere('agenda.id_tabela_preco', 0)
                                    ->orWhere('agenda.id_pedido', 0);
                            } else {
                                $sql->orWhere('agenda.id_modalidade', 0)
                                    ->orWhere('agenda.id_tabela_preco', 0);
                            }


                    }
                }
            })
            ->where('id_tipo_procedimento', '<>', 5)
            ->where('agenda.status', $status)
            ->unionAll(
                DB::table('old_mov_atividades')
                    ->select(
                        "old_mov_atividades.id",
                        DB::raw("(select 1) AS id_emp"),
                        "old_mov_atividades.id_status",
                        "old_mov_atividades.status",
                        "old_mov_atividades.id_tipo_procedimento",
                        "tipo_procedimento.descr AS tipo_procedimento",
                        "old_mov_atividades.id_grade AS id_grade_horario",
                        "old_mov_atividades.hora",
                        "old_mov_atividades.data",
                        "old_contratos.pessoas_id AS id_paciente",
                        "pessoa.nome_fantasia AS nome_paciente",
                        "profissional.nome_fantasia AS nome_profissional",
                        "old_modalidades.descr AS descr_procedimento",
                        "old_financeira.descr AS convenio_nome",
                        "agenda_status.permite_editar",
                        "agenda_status.libera_horario",
                        "agenda_status.permite_fila_espera",
                        "agenda_status.permite_reagendar",
                        "agenda_status.descr AS descr_status",
                        "agenda_status.cor AS cor_status",
                        "agenda_status.cor_letra",
                        DB::raw("(select 0) AS min_intervalo"),
                        DB::raw("(select 0) AS id_confirmacao"),
                        DB::raw("(select '') AS descr_confirmacao"),
                        DB::raw("(select '') AS obs"), DB::raw("(select 1) AS antigo")
                    )
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_atividades.id_contrato')
                    ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_financeira')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                    ->where('old_mov_atividades.lixeira', 0)
                    ->where('old_mov_atividades.data', '>=', $data_inicial->format('Y-m-d'))
                    ->where('old_mov_atividades.data', '<=', $data_final->format('Y-m-d'))
                    ->where(function ($sql) use ($id_membro) {
                        if ($id_membro != 0) {
                            $sql->where('old_mov_atividades.id_membro', $id_membro);
                        }
                    })
                    ->where(function ($sql) use ($completo_incompleto) {
                        if ($completo_incompleto == 'I') {
                            $sql->where('old_mov_atividades.id_pedido', 0)
                                ->where('old_mov_atividades.id_tipo_procedimento', 4);
                        }
                    })
                    ->where('old_mov_atividades.status', $status)
                    ->groupBy(
                        "old_mov_atividades.id",
                        "old_mov_atividades.id_status",
                        "old_mov_atividades.status",
                        "old_mov_atividades.id_tipo_procedimento",
                        "tipo_procedimento.descr",
                        "old_mov_atividades.id_grade",
                        "old_mov_atividades.hora",
                        "old_mov_atividades.data",
                        "old_contratos.pessoas_id",
                        "pessoa.nome_fantasia",
                        "profissional.nome_fantasia",
                        "old_modalidades.descr",
                        "old_financeira.descr",
                        "agenda_status.permite_editar",
                        "agenda_status.libera_horario",
                        "agenda_status.permite_fila_espera",
                        "agenda_status.permite_reagendar",
                        "agenda_status.descr",
                        "agenda_status.cor",
                        "agenda_status.cor_letra"
                    )
            )
            ->orderby('data', 'DESC')
            ->orderby('hora')
            ->get();
        return view('.reports.impresso_agendamentos', compact('agendamentos'));
    }

    public function notificar_participantes(Request $request) {
        $id_paciente = Agenda::find($request->id_agendamento)->id_paciente;

        return $this->notificar($id_paciente, $request->id_agendamento);
        // if($request->opcao == 1) {
        //     $this->notificar(Agenda::find($request->id_agendamento)->id_profissional, $request->id_agendamento);
        // }
    }






    private function notificar($id_pessoa, $id_agendamento)
    {
        $pessoa = Pessoa::find($id_pessoa);
        $agendamento = Agenda::find($id_agendamento);
        
        if ($pessoa->celular1 != '' && $agendamento->notificado == '0') {

            $client = new Client();

            $aux_pessoa = "*" . strtoupper($pessoa->nome_fantasia) . "*";
            $data_hora = "*" . date('d/m/Y', $agendamento->data) . '* às *' . date('H:m',$agendamento->hora) . "*";
            $modalidade = "*" . strtoupper(Procedimento::find($agendamento->id_modalidade)->descr) . "*";
            $membro = "*" . strtoupper(Pessoa::find($agendamento->id_profissional)->nome_fantasia) . "*";
            $endereco = "*" . strtoupper(Empresa::find($agendamento->id_emp)->endereco) . "*";
            $empresa = Empresa::find($agendamento->id_emp)->descr;

            $celular = $pessoa->celular1;
            $celular = str_replace('(', '', $celular);
            $celular = str_replace(')', '', $celular);
            $celular = str_replace(' ', '', $celular);
            $celular = str_replace('-', '', $celular);
            $celular = "55" . $celular;
            $response = $client->request('POST', ZEnviaApi() . 'channels/whatsapp/messages', [
                "headers" => [
                    "Content-Type" => "application/json",
                    "X-API-TOKEN" => "UuDAdhPflhTUagsIQZMDTBkLj8dGMKEN6c3P"
                ],
                "body" => json_encode([
                    "from" => "55119935248881",
                    "to" => $celular,
                    "contents" => [
                        [
                            "type" => "template",
                            "templateId" => "74c621e9-ad70-4f48-a4a5-919016a2a0c2",
                            "fields" => [
                                "associado" => $aux_pessoa,
                                "data_hora" => $data_hora,
                                "modalidade" => $modalidade,
                                "membro" => $membro,
                                "endereco" => $endereco,
                                "empresa" => $empresa
                            ]
                        ]
                    ]
                ])
            ]);
            
            $data = Agenda::find($agendamento->id);
            $data->notificado = '1';
            $data->save();

            $data = new ZEnvia;
            $data->id_agendamento = $agendamento->id;
            $data->text = "";
            $data->direction = "OUT";
            $data->celular = $celular;
            $data->selected = '1';
            $data->save();

        }
    }

}