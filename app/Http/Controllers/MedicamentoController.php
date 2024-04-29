<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if ($request->id == null) $medicamento = new Medicamento;
            else                      $medicamento = Medicamento::find($request->id);

            $medicamento->id_emp = getEmpresa();
            $medicamento->descr = $request->descr;
            $medicamento->uso = $request->uso;
            $medicamento->tipo = $request->tipo;
            $medicamento->unidade = $request->unidade;
            $medicamento->posologia = $request->posologia;
            $medicamento->ativo = $request->ativo;
            $medicamento->save();

            return redirect('/medicamento');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $medicamento = Medicamento::find($request->id);
            $medicamento->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('medicamento')
                ->where('id', $id)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $medicamentos = DB::table('medicamento')
                            ->where('id_emp', getEmpresa())
                            ->get();
            
            return view('medicamento', compact('medicamentos'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('medicamento')
                ->where('id_emp', getEmpresa())
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
