<?php

namespace App\Http\Controllers;

use DB;
use App\GradeBloqueio;
use Illuminate\Http\Request;

class GradeBloqueioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request)
    {
        try {
            if ($request->dia_semana == 0) {
                for ($i = 1; $i <= 7; $i++) {
                    $grade_bloqueio = new GradeBloqueio;
                    $grade_bloqueio->id_profissional = $request->id_profissional;
                    $grade_bloqueio->data_inicial = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_inicial)));
                    $grade_bloqueio->data_final = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_final)));
                    $grade_bloqueio->hora_inicial = $request->hora_inicial;
                    $grade_bloqueio->hora_final = $request->hora_final;
                    $grade_bloqueio->dia_semana = $i;
                    $grade_bloqueio->ativo = true;
                    $grade_bloqueio->obs = $request->obs;
                    $grade_bloqueio->id_emp = getEmpresa();
                    $grade_bloqueio->save();
                }
            } else {
                $grade_bloqueio = new GradeBloqueio;
                $grade_bloqueio->id_profissional = $request->id_profissional;
                $grade_bloqueio->data_inicial = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_inicial)));
                $grade_bloqueio->data_final = date('Y-m-d', strtotime(str_replace("/", "-", $request->data_final)));
                $grade_bloqueio->hora_inicial = $request->hora_inicial;
                $grade_bloqueio->hora_final = $request->hora_final;
                $grade_bloqueio->dia_semana = date('w', strtotime(str_replace("/", "-", $request->data_inicial))) + 1;
                $grade_bloqueio->ativo = true;
                $grade_bloqueio->obs = $request->obs;
                $grade_bloqueio->id_emp = getEmpresa();
                $grade_bloqueio->save();
            }
            return $grade_bloqueio;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar_pessoa($id_pessoa) {
        try {
            $profissional = DB::table('pessoa')
                            ->where('id', $id_pessoa)
                            ->first();

            $bloqueios = DB::table('grade_bloqueio')
                        ->where('id_profissional', $id_pessoa)
                        ->where('data_final', '>=', date('Y-m-d'))
                        ->where('id_emp', getEmpresa())
                        ->orderby('dia_semana')
                        ->get();

            $data['profissional'] = $profissional;
            $data['bloqueios'] = $bloqueios;

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function ativar_desativar(Request $request) {
        try {
            $grade_bloqueio = GradeBloqueio::find($request->id_bloqueio);
            $grade_bloqueio->ativo = ($request->ativacao == 'true');
            $grade_bloqueio->save();

            return $grade_bloqueio;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $grade_bloqueio = GradeBloqueio::find($request->id_bloqueio);
            $grade_bloqueio->delete();

            return $grade_bloqueio;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
