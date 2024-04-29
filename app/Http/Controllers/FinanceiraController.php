<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Financeira;
use App\FinanceiraTaxas;
use Illuminate\Http\Request;

class FinanceiraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        // try {
        //     if (!$request->id) $financeira = new Financeira;
        //     else               $financeira = Financeira::find($request->id);
        //     $financeira->id_emp = getEmpresa();
        //     $financeira->id_pessoa = $request->pessoa_id;
        //     $financeira->descr = $request->descr;
        //     $financeira->save();

        //     return redirect('/financeira');
        // } catch (\Exception $e) {
        //     return $e->getMessage();
        // }
        if ($request->id) $financeira = Financeira::find($request->id);
        else              $financeira = new Financeira;

        $financeira->id_emp         = $request->id_emp;
        $financeira->tipo_de_baixa  = $request->tipo_baixa;
        $financeira->descr          = $request->descr;
        $financeira->prazo          = $request->prazo;
        $financeira->taxa_padrao    = $request->taxa_padrao;

        $financeira->save();

        DB::table('financeira_taxas')
        ->where('id_financeira', $financeira->id)
        ->delete();

        if ($request->redes){
            for($i=0; $i < sizeof($request->redes); $i++) {
                $financeira_taxa = new FinanceiraTaxas;

                $financeira_taxa->id_financeira   = $financeira->id;
                $financeira_taxa->rede_adquirente = $request->redes[$i];
                $financeira_taxa->max_parcela     = $request->parcelas[$i];
                $financeira_taxa->taxa            = $request->taxas[$i];

                $financeira_taxa->save();
            }
        }
        return 'true';

    }

    public function deletar(Request $request) {
        try {
            $financeira = Financeira::find($request->id);
            $financeira->delete();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        $financeiras = DB::table('financeira')
                ->select(
                    'financeira.id',
                    'financeira.descr',
                    'financeira.prazo',
                    'financeira.taxa_padrao',
                    'financeira.tipo_de_baixa'
                )
                ->get();
                
        $empresas = DB::table('empresa')
                    ->get();
                
        return view('financeira', compact('financeiras', 'empresas'));
    }

    public function mostrar($id) {
        $data = new \StdClass;
        $data->financeira = Financeira::find($id);
        $data->taxas = DB::table('financeira_taxas')
                        ->where('id_financeira', $id)
                        ->get();
        
        return json_encode($data);
    }
}
