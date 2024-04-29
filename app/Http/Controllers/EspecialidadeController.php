<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Especialidade;
use Illuminate\Http\Request;

class EspecialidadeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $especialidade = new especialidade;
            else               $especialidade = especialidade::find($request->id);
            $especialidade->id_emp = getEmpresa();
            $especialidade->descr = $request->descr;
            $especialidade->externo = $request->externo === 'on';
            $especialidade->save();
            return redirect('/especialidade');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $especialidade = especialidade::find($request->id_especialidade);
            $especialidade->lixeira = true;
            $especialidade->save();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $especialidades = DB::table('especialidade')
                            // ->where('id_emp', getEmpresa())
                            ->where('lixeira', false)
                            ->orderby('descr')
                            ->get();
            
            return view('especialidade', compact('especialidades'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('especialidade')
                // ->where('id_emp', getEmpresa())
                ->where('lixeira', false)
                ->orderby('descr')
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('especialidade')
                // ->where('id_emp', getEmpresa())
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
