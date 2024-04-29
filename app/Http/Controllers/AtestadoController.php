<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Atestado;
use Illuminate\Http\Request;

class AtestadoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $atestado = new Atestado;
            $atestado->id_emp = getEmpresa();
            $atestado->id_profissional = Auth::user()->id_profissional;
            $atestado->id_paciente = $request->id_paciente;
            $atestado->cid = $request->cid;
            $atestado->data = date('Y-m-d', strtotime(str_replace("/", "-", $request->data)));
            $atestado->periodo = $request->periodo;
            $atestado->save();

            return $atestado;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function deletar(Request $request) {
        try {
            $atestado = Atestado::find($request->id);
            $atestado->delete();

            return $atestado;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function listar($id_paciente) {
        try {
            return json_encode(
                DB::table('atestado')
                ->select(
                    'atestado.id',
                    'atestado.data',
                    'atestado.cid',
                    'atestado.data',
                    'atestado.periodo',
                    'pessoa.nome_fantasia AS profissional_descr'
                )
                ->leftjoin('pessoa', 'pessoa.id', 'atestado.id_profissional')
                ->where('atestado.id_paciente', $id_paciente)
                ->get()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $atestado = DB::table('atestado')
                        ->select(
                            'atestado.id',
                            'atestado.data',
                            'atestado.cid',
                            'atestado.data',
                            'atestado.periodo',
                            'paciente.nome_fantasia AS paciente_nome',
                            'paciente.sexo AS paciente_sexo'
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'atestado.id_paciente')
                        ->where('atestado.id', $id)
                        ->first();

            if (getEmpresaObj()->mod_impressao_especifica) {
                return view('.reports.' . getEmpresa() . '.impresso_atestado', compact('atestado'));
            } else {
                return view('.reports.impresso_atestado', compact('atestado'));
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}