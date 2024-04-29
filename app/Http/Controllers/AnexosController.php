<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Anexos;
use Illuminate\Http\Request;

class AnexosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request)
    {
        try {
            if ($request->file('arquivo') != null) {
                $anexo = new Anexos;
                $anexo->id_emp = getEmpresa();
                $anexo->id_paciente = $request->id_paciente;
                $anexo->id_profissional = Auth::user()->id_profissional;
                $anexo->obs = $request->obs;
                $anexo->pasta = $request->pasta;
                $anexo->created_at = date('Y-m-d H:i:s');
                $anexo->updated_at = date('Y-m-d H:i:s');
                $anexo->save();
            
                $path = $request->file('arquivo')->getClientOriginalName();
                print_r($path);
                $request->file('arquivo')
                        ->move(
                            public_path('anexos'),
                            $path
                        );
                $anexo->titulo = $path;
                $anexo->save();
            }
            return json_encode($anexo);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function baixar($id)
    {
        $fileName = DB::table('anexos')
                        ->where('id', $id)
                        ->value('titulo');

        return response()->download(public_path('anexos') . '/' . $fileName);
    }

    public function deletar(Request $request)
    {
        try {
            $anexo = Anexos::findOrFail($request->id);
            $anexo->lixeira = 1;
            $anexo->save();

            return $anexo;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarPorPessoa($id_pessoa, $pasta) {
        try {
            $anexos = DB::table('anexos')
                    ->where('id_paciente', $id_pessoa)
                    ->where('lixeira', 0)
                    ->where(function($sql) use($pasta) {
                        if ($pasta > 0) {
                            $sql->where('pasta', $pasta);
                        }
                    })
                    ->orderby('created_at', 'DESC')
                    ->get();

            return json_encode($anexos);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
