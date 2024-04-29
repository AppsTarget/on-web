<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\FinanceiraFormasPag;
use Illuminate\Http\Request;

class FinanceiraFormasPagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $financeira_formas_pag = new FinanceiraFormasPag;
            else               $financeira_formas_pag = FinanceiraFormasPag::find($request->id);
            $financeira_formas_pag->id_emp = getEmpresa();
            $financeira_formas_pag->id_forma_pag = $request->id_forma_pag;
            $financeira_formas_pag->id_financeira = $request->financeira_id;
            $financeira_formas_pag->save();

            return $financeira_formas_pag;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $financeira_formas_pag = FinanceiraFormasPag::find($request->id);
            $financeira_formas_pag->delete();

            return $financeira_formas_pag;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar($id_forma_pag) {
        return json_encode(
            DB::table('financeira_formas_pag')
            ->select(
                'financeira_formas_pag.id',
                'financeira.id AS id_financeira',
                'financeira.descr AS descr_financeira',
                'forma_pag.id AS id_forma_pag',
                'forma_pag.descr AS descr_forma_pags'
            )
            ->leftjoin('financeira', 'financeira.id', 'financeira_formas_pag.id_financeira')
            ->leftjoin('forma_pag', 'forma_pag.id', 'financeira_formas_pag.id_forma_pag')
            ->where('financeira_formas_pag.id_forma_pag', $id_forma_pag)
            ->get()
        );
    }

    public function mostrar($id) {
        return json_encode(
            DB::table('financeira_formas_pag')
            ->where('id', $id)
            ->first()
        );
    }

    public function listar_financeiras($id) {
        return DB::table('financeira_formas_pag')
               ->select('financeira.id', 'financeira.descr')
               ->leftjoin('financeira', 'financeira.id', 'financeira_formas_pag.id_financeira')
               ->where('financeira_formas_pag.id_forma_pag', $id)
               ->get();
    }
}
