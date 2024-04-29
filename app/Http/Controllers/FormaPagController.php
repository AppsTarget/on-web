<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\FormaPag;
use Illuminate\Http\Request;

class FormaPagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $forma_pag = new FormaPag;
            else               $forma_pag = FormaPag::find($request->id);
            $forma_pag->id_emp = getEmpresa();
            $forma_pag->descr = $request->descr;
            $forma_pag->max_parcelas = $request->max_parcelas;
            $forma_pag->dias_entre_parcela = $request->dias_entre_parcela;
            $forma_pag->avista_prazo = $request->avista_prazo;
            $forma_pag->save();

            return redirect('/forma-pag');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $forma_pag = FormaPag::find($request->id);
            $forma_pag->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        $forma_pag = DB::table('forma_pag')
                // ->where('id_emp', getEmpresa())
                ->get();

        return view('forma_pag', compact('forma_pag'));
    }

    public function listar_tipo($tipo) {
        if ($tipo == 'E') $tipo = 'P';
        try {
            return json_encode(
                DB::table('forma_pag')
                // ->where(function($sql) use($tipo) {
                //     if ($tipo != 'P') {
                //         $sql->where('avista_prazo', $tipo);
                //     }
                // })
                ->where('lixeira', false)
                // ->where('id_emp', getEmpresa())
                ->orderby('descr')
                ->get()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        $forma_pag = DB::table('forma_pag')
                    ->where('id', $id)
                    ->first();

        $forma_pag->financeiras =
            DB::table('financeira_formas_pag')
            ->select(
                'financeira.id',
                'financeira.descr'
            )
            ->join('financeira', 'financeira.id', 'financeira_formas_pag.id_financeira')
            ->where('financeira_formas_pag.id_forma_pag', $id)
            // ->where('financeira.id_emp', getEmpresa())
            ->get();

        return json_encode($forma_pag);
    }

    public function consulta_descr($descr) {
        $forma_pag = DB::table('forma_pag')
                    ->where('descr', $descr)
                    ->first();

        return json_encode($forma_pag);
    }
}
