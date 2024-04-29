<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\TipoProcedimento;
use App\Procedimento;   
use App\Especialidade;
use Illuminate\Http\Request;

class TipoProcedimentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $tipo_procedimento = new TipoProcedimento;
            else               $tipo_procedimento = TipoProcedimento::find($request->id);

            $tipo_procedimento->id_emp = getEmpresa();

            if ($request->assossiar_especialidade == 'true') $tipo_procedimento->assossiar_especialidade = true;
            else                                             $tipo_procedimento->assossiar_especialidade = false;
            if ($request->assossiar_contrato == 'true')      $tipo_procedimento->assossiar_contrato = true;
            else                                             $tipo_procedimento->assossiar_contrato = false;

            $tipo_procedimento->id_especialidade            = $request->especialidade;

            $tipo_procedimento->descr = $request->descr;
            $tipo_procedimento->tempo_procedimento = $request->tempo_procedimento;
            $tipo_procedimento->save();

            return $tipo_procedimento;
            // return redirect("/tipo-procedimento");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $tipo_procedimento = TipoProcedimento::find($request->id_tipo_procedimento);
            $tipo_procedimento->lixeira = true;
            $tipo_procedimento->save();

            return $tipo_procedimento;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $tipo_procedimentos = DB::table('tipo_procedimento')
                            ->where('id_emp', getEmpresa())
                            ->where('lixeira', false)
                            ->get();
            $especialidades = DB::table('especialidade')
                            ->where('id_emp', getEmpresa())
                            ->get();
            $tabela_precos = DB::table('tabela_precos')
                            ->where('id_emp', getEmpresa())
                            ->get();

            return view('tipo_procedimento', compact('tipo_procedimentos', 'especialidades', 'tabela_precos'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('tipo_procedimento')
                ->where('id_emp', getEmpresa())
                ->where('lixeira', false)
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }   

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('tipo_procedimento')
                ->where('id_emp', getEmpresa())
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
}
