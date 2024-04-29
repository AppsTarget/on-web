<?php

namespace App\Http\Controllers;

use DB;
use App\Etiqueta;
use Illuminate\Http\Request;

class EtiquetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $etiqueta = new Etiqueta;
            else               $etiqueta = Etiqueta::find($request->id);
            $etiqueta->id_emp = getEmpresa();
            $etiqueta->descr = $request->descr;
            $etiqueta->cor = $request->cor;
            $etiqueta->save();

            return redirect('/etiqueta');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $etiqueta = Etiqueta::find($request->id);
            $etiqueta->delete();
            return $etiqueta;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $etiquetas = DB::table('etiqueta')
                            ->where('id_emp', getEmpresa())
                            ->get();
            
            return view('etiqueta', compact('etiquetas'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('etiqueta')
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
                DB::table('etiqueta')
                ->where('id_emp', getEmpresa())
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
