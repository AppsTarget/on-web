<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if ($request->id == null) $documento = new Documento;
            else                      $documento = DocumentoModelo::find($request->id);

            $documento->id_emp = getEmpresa();
            $documento->id_profissional = getProfissional()->id;
            $documento->id_paciente = $request->id_paciente;
            $documento->id_doc_modelo = $request->id_doc_modelo;
            $documento->corpo = $request->corpo;
            $documento->pasta = $request->pasta;
            $documento->save();



            return redirect('/pessoa/prontuario/' . $request->id_paciente);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $documento = Documento::find($request->id);
            $documento->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('documento')
                ->where('id', $id)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            return json_encode(
                DB::table('documento')
                ->where('id_emp', getEmpresa())
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function imprimir($id) {
        try {
            $documento = DB::table('documento')
                        ->select(
                            'documento.*',
                            'documento_modelo.titulo'
                        )
                        ->leftjoin('documento_modelo', 'documento_modelo.id', 'documento.id_doc_modelo')
                        ->where('documento.id', $id)
                        ->first();

            $paciente = DB::table('pessoa')
                        ->where('id', $documento->id_paciente)
                        ->first();

            if (getEmpresaObj()->mod_impressao_especifica) {
                return view(
                    '.reports.' . getEmpresa() . '.impresso_documento',
                    compact('documento', 'paciente')
                );
            } else {
                return view(
                    '.reports.impresso_documento',
                    compact('documento', 'paciente')
                );
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa, $pasta) {
        try {
            $documentos = DB::table('documento')
                        ->select(
                            'documento.*',
                            'paciente.nome_fantasia AS descr_paciente',
                            'profissional.nome_fantasia AS descr_profissional',
                            'documento_modelo.titulo',
                            'documento_modelo.corpo AS corpo_modelo'
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'documento.id_paciente')
                        ->leftjoin('pessoa AS profissional', 'profissional.id', 'documento.id_profissional')
                        ->leftjoin('documento_modelo', 'documento_modelo.id', 'documento.id_doc_modelo')
                        ->where('documento.id_paciente', $id_pessoa)
                        ->whereRaw('(documento.pasta = '.$pasta.' OR '.$pasta.' = 0)')
                        ->orderby('documento.created_at', 'DESC')
                        ->get();

            return json_encode($documentos);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
