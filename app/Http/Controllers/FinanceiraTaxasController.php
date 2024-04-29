<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\FinanceiraTaxas;
use Illuminate\Http\Request;

class FinanceiraTaxasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $financeira_taxa = new FinanceiraTaxas;
            else               $financeira_taxa = FinanceiraTaxas::find($request->id);
            $financeira_taxa->id_emp = getEmpresa();
            $financeira_taxa->id_financeira = $request->id_financeira;
            $financeira_taxa->num_min = $request->num_min;
            $financeira_taxa->num_max = $request->num_max;
            $financeira_taxa->taxa = $request->taxa;
            $financeira_taxa->save();

            return $financeira_taxa;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $financeira_taxa = FinanceiraTaxas::find($request->id);
            $financeira_taxa->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar($id_financeira) {
        return json_encode(
            DB::table('financeira_taxas')
            ->where('id_financeira', $id_financeira)
            ->orderby('num_min')
            ->get()
        );
    }

    public function mostrar($id) {
        return json_encode(
            DB::table('financeira_taxas')
            ->where('id', $id)
            ->first()
        );
    }
}
