<?php

namespace App\Http\Controllers;

use DB;
use DateTime;
use DateInterval;
use App\Grade;
use App\GradeHorario;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function mostrar_pessoa($id_pessoa, $id_emp) {
        try {
            $empresas = DB::table('empresas_profissional')
                        ->select('empresa.id', 'empresa.descr')
                        ->leftjoin('empresa', 'empresa.id', 'empresas_profissional.id_emp')
                        ->where('empresas_profissional.id_profissional', $id_pessoa)
                        ->get();
                        
            $profissional = DB::table('pessoa')
                            ->where('id', $id_pessoa)
                            ->first();

            if ($id_emp == 0) {
                $grade = DB::table('grade')
                        ->where('id_profissional', $id_pessoa)
                        ->where('id_emp', $empresas[0]->id)
                        ->where('lixeira', 'N')
                        ->orderby('dia_semana')
                        ->get();
            }
            else {
                $grade = DB::table('grade')
                        ->where('id_profissional', $id_pessoa)
                        ->where('id_emp', $id_emp)
                        ->where('lixeira', 'N')
                        ->orderby('dia_semana')
                        ->get();
            }
            

            $data = new \StdClass;
            $data->profissional = $profissional;
            $data->empresas = $empresas;
            $data->grade = $grade;

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function salvar(Request $request)
    {
        try {

            if ($request->mesclar) {
                $grade = new Grade;
                $grade->id_emp = $request->empresa;
                $grade->id_profissional = $request->id_profissional;
                $grade->id_etiqueta = $request->etiqueta;
                $grade->dia_semana = $request->dia_semana;
                if ($request->data_inicial != '') $grade->data_inicial = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_inicial)));
                else                              $grade->data_inicial = date('Y-m-d');
                if ($request->data_final != '') $grade->data_final = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_final)));
                $grade->hora_inicial = $request->hora_inicio;
                $grade->hora_final = $request->hora_final;
                $grade->max_qtde_pacientes = $request->max_qtde_pacientes;
                $grade->min_intervalo = $request->min_intervalo;
                $grade->ativo = true;
                $grade->obs = $request->obs;
                $grade->save();

                $bHorario = false;
                $zero_date = new DateTime(date('Y-m-d') . ' ' . $request->hora_inicio);
                $final_date = new DateTime(date('Y-m-d') . ' ' . $request->hora_final);
                while (!$bHorario) {
                    if ($zero_date >= $final_date) $bHorario = true;
                    else {
                        $grade_horario = new GradeHorario;
                        $grade_horario->id_grade = $grade->id;
                        $grade_horario->hora = $zero_date->format('H:i');
                        $grade_horario->dia_semana = $request->dia_semana;
                        $grade_horario->save();
                        $zero_date->add(new DateInterval('PT' . $request->min_intervalo . 'M'));
                    }
                }
                return $grade->id_profissional;
            } else {

            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function deletar(Request $request) {
        try {
            DB::statement("update grade set lixeira = 'S' where id = ".$request->id);
            DB::statement("update grade set data_final = '".date('Y-m-d')."' where id = ".$request->id);
            $grade = Grade::find($request->id);
           // $grade->delete();

            return $grade->id_profissional;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function ativar_desativar(Request $request) {
        try {
            $grade = Grade::find($request->id_grade);
            $grade->ativo = ($request->ativacao == 'true');
            $grade->save();

            return $grade->id_profissional;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function dividir_horario(Request $request) {
        try {
            $dia_semana = (date('w', strtotime($request->dia)) + 1);
            $grade_referencia = DB::table('grade_horario')
                    ->select('grade.*')
                    ->join('grade', 'grade.id', 'grade_horario.id_grade')
                    ->where('grade.id_profissional', $request->id_profissional)
                    ->where('grade_horario.dia_semana', $dia_semana)
                    ->where('grade_horario.hora', $request->hora)
                    ->where('grade.lixeira', 'N')
                    ->orderby('grade_horario.created_at', 'DESC')
                    ->first();

            $grade = new Grade;
            $grade->id_profissional = $request->id_profissional;
            $grade->id_etiqueta = $grade_referencia->id_etiqueta;
            $grade->dia_semana = $dia_semana;
            $grade->data_inicial = date('Y-m-d', strtotime($request->dia));
            $grade->data_final = date('Y-m-d', strtotime($request->dia));

            $horario_inicial = new DateTime(date('Y-m-d') . ' ' . $request->hora);
            $horario_inicial = $horario_inicial->add(new DateInterval('PT' . round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN) . 'M'));
            $horario_inicial = $horario_inicial->format('H:i');
            $grade->hora_inicial = $horario_inicial;

            $hora_final = new DateTime(date('Y-m-d') . ' ' . $request->hora);
            $hora_final = $hora_final->add(new DateInterval('PT' . $grade_referencia->min_intervalo . 'M'));
            $hora_final = $hora_final->format('H:i');
            $grade->hora_final = $hora_final;

            $grade->min_intervalo = round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN);

            $grade->max_qtde_pacientes = null;
            $grade->grade_divisao = true;
            $grade->ativo = true;
            $grade->obs = 'Grade gerada ao dividir horário!';
            $grade->save();

            $grade_horario = new GradeHorario;
            $grade_horario->id_grade = $grade->id;
            $grade_horario->hora = $horario_inicial;
            $grade_horario->dia_semana = $dia_semana;
            $grade_horario->save();

            return $grade;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function dividir_horario_por_id(Request $request) {
        try {
            $agenda_referencia = DB::table('agenda')
                                ->where('id', $request->id_agendamento)
                                ->first();

            $grade_referencia = DB::table('grade')
                                ->select('grade.*')
                                ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
                                ->where('grade_horario.id', $agenda_referencia->id_grade_horario)
                                ->where('grade.lixeira', 'N')
                                ->first();

            $dia_semana = (date('w', strtotime($agenda_referencia->data)) + 1);

            $grade = new Grade;
            $grade->id_profissional = $agenda_referencia->id_profissional;
            $grade->id_etiqueta = $grade_referencia->id_etiqueta;
            $grade->dia_semana = $dia_semana;
            $grade->data_inicial = date('Y-m-d', strtotime($agenda_referencia->data));
            $grade->data_final = date('Y-m-d', strtotime($agenda_referencia->data));

            $horario_inicial = new DateTime(date('Y-m-d') . ' ' . $agenda_referencia->hora);
            $horario_inicial = $horario_inicial->add(new DateInterval('PT' . round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN) . 'M'));
            $horario_inicial = $horario_inicial->format('H:i');
            $grade->hora_inicial = $horario_inicial;

            $hora_final = new DateTime(date('Y-m-d') . ' ' . $agenda_referencia->hora);
            $hora_final = $hora_final->add(new DateInterval('PT' . $grade_referencia->min_intervalo . 'M'));
            $hora_final = $hora_final->format('H:i');
            $grade->hora_final = $hora_final;

            $grade->min_intervalo = round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN);

            $grade->max_qtde_pacientes = null;
            $grade->grade_divisao = true;
            $grade->ativo = true;
            $grade->obs = 'Grade gerada ao dividir horário!';
            $grade->save();

            $grade_horario = new GradeHorario;
            $grade_horario->id_grade = $grade->id;
            $grade_horario->hora = $horario_inicial;
            $grade_horario->dia_semana = $dia_semana;
            $grade_horario->save();

            return $grade;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function verificar_grade_por_horario(Request $request) {
        try {
            $dia_semana = (date('w', strtotime($request->dia)) + 1);

            $id_grade = DB::table('grade')
                    ->leftjoin('pessoa', 'pessoa.id', 'grade.id_profissional')
                    ->where('grade.dia_semana', $dia_semana)
                    ->where('grade.data_inicial', '<=', $request->dia)
                    ->where(function($sql) use ($request) {
                        $sql->whereNull('grade.data_final')
                            ->orWhere('grade.data_final', '>=', $request->dia);
                    })
                    ->where('grade.hora_inicial', $request->hora)
                    ->where('pessoa.id_emp', getEmpresa())
                    ->where('grade.grade_divisao', true)
                    ->where('grade.lixeira', 'N')
                    ->value('grade.id');

            return $id_grade;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar_grade_por_horario(Request $request) {
        try {
            $dia_semana = (date('w', strtotime($request->dia)) + 1);

            $id_grade = DB::table('grade')
                    ->select('grade.hora_inicial','grade.hora_final', 'grade.dia_semana')
                    ->leftjoin('grade_horario', 'grade_horario.id_grade', 'grade.id')
                    ->where('grade_horario.id', $request->id_grade)
                    ->where('grade.lixeira', 'N')
                    ->first();

            return json_encode($id_grade);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function verificar_grade_por_dia_semana(Request $request) {
        try {
            return strval(DB::table('grade')
                    ->where('id_profissional', $request->id_profissional)
                    ->where('dia_semana', $request->dia_semana)
                    ->where('lixeira', 'N')
                    ->exists());

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    function listar_todos_horarios (){
        return DB::table('grade_horario')
               ->select('hora AS hora')
               ->join('grade', 'grade.id', 'grade_horario.id_grade')
               ->where('ativo', true)
               ->where('grade.lixeira', 'N')
               ->groupBy('hora')
               ->get();
    }
}