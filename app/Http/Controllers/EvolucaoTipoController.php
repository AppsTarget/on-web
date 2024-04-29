<?php

namespace App\Http\Controllers;

use DB;
use App\EvolucaoTipo;
use Illuminate\Http\Request;

class EvolucaoTipoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if ($request->id == null) $evolucao_tipo = new EvolucaoTipo;
            else                      $evolucao_tipo = EvolucaoTipo::find($request->id);
            $evolucao_tipo->id_emp = getEmpresa();
            $evolucao_tipo->descr = $request->descr;
            $evolucao_tipo->prioritario = ($request->prioritario == 'on');
            $evolucao_tipo->save();

            return redirect('/evolucao-tipo');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $evolucao_tipo = EvolucaoTipo::find($request->id);
            $evolucao_tipo->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('evolucao_tipo')
                ->where('id', $id)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $evolucoes_tipo = DB::table('evolucao_tipo')
                            // ->where('id_emp', getEmpresa())
                            ->get();
            
            return view('evolucao_tipo', compact('evolucoes_tipo'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('evolucao_tipo')
                // ->where('id_emp', getEmpresa())
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
