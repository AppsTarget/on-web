<?php

namespace App\Http\Controllers;

use DB;
use App\GradeHorario;
use Illuminate\Http\Request;

class GradeHorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $grade_horario = new GradeHorario;
            $grade_horario->id_grade = $request->id_grade;
            $grade_horario->hora = $request->hora;
            $grade_horario->dia_semana = $request->dia_semana;
            $grade_horario->save();
            
            return $grade_horario;
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }

    public function listar(Request $request) {

        try {
            $horarios = DB::table('grade_horario')
                ->where('dia_semana', $request->dia_semana)
                ->get();

            return $horarios;
        } catch (\Exception $e) {
            return $e->getMessage();
        } 

    }

}
