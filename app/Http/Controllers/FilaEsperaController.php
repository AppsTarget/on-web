<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Agenda;
use App\FilaEspera;
use Illuminate\Http\Request;

class FilaEsperaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // E - Em Espera | D - Desistência | A - Atendimento | F - Finalizado
    public function salvar(Request $request) {
        try {
            $agendamento = DB::table('agenda')
                            ->where('agenda.id', $request->id_agendamento)
                            ->first();

            $fila_espera = new FilaEspera;
            $fila_espera->id_emp = getEmpresa();
            $fila_espera->id_profissional = $agendamento->id_profissional;
            $fila_espera->id_paciente = $agendamento->id_paciente;
            $fila_espera->id_agendamento = $request->id_agendamento;
            $fila_espera->data_chegada = date('Y-m-d');
            $fila_espera->hora_chegada = $request->hora_chegada;
            $fila_espera->status = 'E';
            $fila_espera->save();

            return redirect('agenda');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function atender_fila(Request $request) {
        try {
            $fila_espera = FilaEspera::find($request->id_fila_espera);
            $fila_espera->status = 'A';
            $fila_espera->save();

            return $fila_espera;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function desistir_fila(Request $request) {
        try {
            $fila_espera = FilaEspera::find($request->id_fila_espera);
            $fila_espera->status = 'D';
            $fila_espera->save();

            return $fila_espera;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar(Request $request) {
        try {
            $fila_espera = DB::table('fila_espera')
                    ->select(
                        'fila_espera.id',
                        'fila_espera.status',
                        'pessoa.id AS paciente_id',
                        'pessoa.nome_fantasia AS paciente_nome',
                        'agenda.hora',
                        'fila_espera.hora_chegada'
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'fila_espera.id_paciente')
                    ->leftjoin('agenda', 'agenda.id', 'fila_espera.id_agendamento')
                    ->where('fila_espera.id_profissional', $request->id_profissional)
                    ->where(function($sql) {
                        $sql->where('fila_espera.status', 'D')
                            ->where('agenda.data', date('Y-m-d'))
                            ->orWhere('fila_espera.status', '<>','F');
                    })
                    ->where('agenda.status', '<>', 'C')
                    ->where('agenda.status', '<>','F')
                    ->orderby('fila_espera.hora_chegada')
                    ->get();


            // $fila_espera = DB::table('agenda')
            //             ->select(
            //                 'agenda_status.descr AS status',
            //                 'agenda.id_paciente',
            //                 'pessoa.nome_fantasia AS nome_paciente',
            //                 'pessoa.data_nasc',
            //                 'agenda.data',
            //                 'agenda.hora',
            //                 DB::raw(
            //                     '(SELECT historico_agenda.created_at' .
            //                     '   FROM historico_agenda ' .
            //                     '  WHERE historico_agenda.id_agenda = agenda.id' .
            //                     '    AND historico_agenda.id_status = agenda_status.id' .
            //                     '  LIMIT 1) AS data_chegada'
            //                 ),
            //                 DB::raw(
            //                     '(SELECT a2.data' .
            //                     '   FROM agenda a2' .
            //                     '   LEFT OUTER JOIN agenda_status AS status ON status.id = a2.id_status' .
            //                     '  WHERE a2.id_paciente = a2.id_paciente' .
            //                     '    AND a2.id   <> agenda.id' .
            //                     '    AND a2.data  < agenda.data' .
            //                     '    AND status.libera_horario = 0' .
            //                     '  ORDER BY a2.data DESC' .
            //                     '  LIMIT 1) AS ultima_consulta'
            //                 )
            //             )
            //             ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
            //             ->join('agenda_status', 'agenda_status.id', 'agenda.id_status')
            //             ->where('agenda_status.permite_fila_espera', true)
            //             ->where('agenda.id_profissional', Auth::user()->id_profissional)
            //             ->orderby('data_chegada')
            //             ->get();

            // foreach ($fila_espera as $paciente) {
            //     $paciente->foto = null;
            //     $path = database_path('pessoa') . '/' . getEmpresa() . '/' . $paciente->id_paciente . '.jpg';
            //     if (file_exists($path)) {
            //         $paciente->foto = base64_encode(file_get_contents($path));
            //     }
            //     if ($paciente->data_nasc != null) {
            //         $birthDate = date('d/m/Y', strtotime($paciente->data_nasc));
            //         $birthDate = explode("/", $birthDate);
            //         $paciente->idade = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
            //                         ? ((date("Y") - $birthDate[2]) - 1)
            //                         : (date("Y") - $birthDate[2])) . ' anos';
            //     } else {
            //         $paciente->idade = 'Sem data de Nascimento.';
            //     }

            //     if ($paciente->ultima_consulta == null) $paciente->ultima_consulta = 'Sem atendimentos.';
            //     else                                    $paciente->ultima_consulta = date('d/m/Y', strtotime($paciente->ultima_consulta));
            // }
            return json_encode($fila_espera);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    // E - Em Espera | D - Desistência | A - Atendimento | F - Finalizado
    public function listar_profissional($id_profissional) {
        try {
            //if (Auth::user()->id_profissional != null) {
                $pessoa = DB::table('pessoa')
                ->where('id', $id_profissional)
                ->first();
                
                if (getProfissional()->colaborador == 'P') {
                    $profissionais = DB::table('pessoa')
                    ->where('id_emp', getEmpresa())
                    ->where('colaborador', 'P')
                    ->where('lixeira', false)
                    ->where('id', $id_profissional)
                    ->get();
                } else {
                    $profissionais = DB::table('pessoa')
                    ->where('id_emp', getEmpresa())
                    ->where('colaborador', 'P')
                    ->where('lixeira', false)
                    ->get();
                }

                $fila_espera = DB::table('agenda')
                            ->select(
                                'agenda.id',
                                'agenda_status.descr AS status',
                                'agenda.data',
                                'agenda.hora',
                                DB::raw(
                                    '(SELECT historico_agenda.created_at' .
                                    '   FROM historico_agenda ' .
                                    '  WHERE historico_agenda.id_agenda = agenda.id' .
                                    '    AND historico_agenda.id_status = agenda_status.id' .
                                    '  LIMIT 1) AS data_chegada'
                                ),
                                'agenda.id_paciente',
                                'pessoa.nome_fantasia AS nome_paciente',
                                'pessoa.data_nasc',
                                'tipo_procedimento.descr AS descr_tipo_procedimento',
                                'convenio.descr AS descr_convenio'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                            ->join('agenda_status', 'agenda_status.id', 'agenda.id_status')
                            ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                            ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                            ->where('agenda_status.permite_fila_espera', true)
                            ->where('agenda.id_profissional', $id_profissional)
                            ->where('agenda.data', date('Y-m-d'))
                            ->where('agenda.status', '<>', 'F')
                            ->orderby('data_chegada')
                            ->get();

                foreach ($fila_espera as $paciente) {
                    if ($paciente->data_nasc != null) {
                        $birthDate = date('d/m/Y', strtotime($paciente->data_nasc));
                        $birthDate = explode("/", $birthDate);
                        $paciente->idade = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                                        ? ((date("Y") - $birthDate[2]) - 1)
                                        : (date("Y") - $birthDate[2])) . ' anos';
                    } else {
                        $paciente->idade = 'Sem data de Nascimento.';
                    }
                }

                return view('fila_espera', compact('fila_espera', 'profissionais', 'pessoa'));
            //} else return redirect('/');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function confirmar(Request $request) {
        try {
            $agenda_confirma = Agenda::find($request->id_agenda);
            $agenda_confirma->status = 'F';
            $agenda_confirma->save();

            return $agenda_confirma;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
