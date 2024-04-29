<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Atendimento;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function comecar_atendimento(Request $request) {
        try {
            $atendimento = new Atendimento;
            $atendimento->id_emp = getEmpresa();
            $atendimento->id_paciente = $request->id_paciente;
            $atendimento->id_profissional = Auth::user()->id_profissional;
            $atendimento->id_agendamento = $request->id_agendamento;
            $atendimento->data_inicio = date("Y-m-d");
            $atendimento->hora_inicio = date("H:i:s");
            $atendimento->created_by = Auth::user()->name;
            $atendimento->updated_by = Auth::user()->name;
            $atendimento->save();

            return $atendimento->id;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function parar_atendimento(Request $request) {
        try {
            return DB::table('atendimento')
                    ->where('id_paciente', $request->id_paciente)
                    ->whereNull('data_fim')
                    ->whereNull('hora_fim')
                    ->update([
                        'data_fim' => date('Y-m-d'),
                        'hora_fim' => date('H:i:s')
                    ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function paciente_em_aberto($id_paciente) {
        try {
            return json_encode(
                DB::table('atendimento')
                ->where('id_paciente', $id_paciente)
                ->whereNull('data_fim')
                ->whereNull('hora_fim')
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function profissional_em_aberto($id_profissional) {
        try {
            return json_encode(
                DB::table('atendimento')
                ->where('id_profissional', $id_profissional)
                ->whereNull('data_fim')
                ->whereNull('hora_fim')
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
