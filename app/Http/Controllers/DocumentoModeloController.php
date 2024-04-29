<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\DocumentoModelo;
use Illuminate\Http\Request;

class DocumentoModeloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if ($request->id == null) $doc_modelo = new DocumentoModelo;
            else                      $doc_modelo = DocumentoModelo::find($request->id);

            $doc_modelo->id_emp = getEmpresa();
            $doc_modelo->titulo = $request->titulo;
            $doc_modelo->corpo = $request->corpo;
            $doc_modelo->ativo = $request->ativo;
            $doc_modelo->save();

            return redirect('/documento-modelo');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $doc_modelo = DocumentoModelo::find($request->id);
            $doc_modelo->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('documento_modelo')
                ->where('id', $id)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $doc_modelos = DB::table('documento_modelo')
                            // ->where('id_emp', getEmpresa())
                            ->get();
            
            return view('documento_modelo', compact('doc_modelos'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('documento_modelo')
                // ->where('id_emp', getEmpresa())
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}