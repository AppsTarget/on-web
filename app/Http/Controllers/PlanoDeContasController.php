<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use \App\PlanoDeContas;
use Illuminate\Http\Request;

class PlanoDeContasController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }


    public function index(){
        return view('plano_de_contas');
    }

    public function montar_arvore() {
        $data = new \stdClass;

        $data->inicial = DB::table('plano_de_contas')
                         ->select('id', 'descr')
                         ->where('lixeira', 0)
                         ->where('id_pai', 0)
                         ->get();

        $data->final   = DB::table('plano_de_contas')
                         ->select('id', 'descr', 'id_pai')
                         ->where('lixeira', 0)
                         ->where('id_pai', '<>', 0)
                         ->get();

        return json_encode($data);
    }

    public function abrir_modal(Request $request) {
        $plano =  DB::table('plano_de_contas')
                    ->select('plano_de_contas.id     AS id',
                             'plano_de_contas.descr  AS descr',
                             'plano_de_contas2.id    AS id_pai',
                             'plano_de_contas2.descr AS descr_pai')
                    ->leftjoin('plano_de_contas As plano_de_contas2', 'plano_de_contas2.id', 'plano_de_contas.id_pai')
                    ->where('plano_de_contas.id', $request->id)
                    ->first();
        
        if ($plano) return json_encode($plano);
        else        return 'false';
    }

    public function salvar(Request $request) {
        if ($request->id != 0) $plano = PlanoDeContas::find($request->id);
        else                   $plano = new PlanoDeContas;

        $plano->id_pai = $request->id_pai;
        $plano->id_emp = getEmpresa();

        if ($request->descr_filho == '') {
            $plano->descr = $request->descr_pai;
        }
        else {
            $plano->descr = $request->descr_filho;
        }
        $plano->lixeira = 0;
        $plano->save();

        return 'true';
    }


    public function deletar(Request $request) {
        $deletar = PlanoDeContas::find($request->id);
        $deletar->lixeira = 1;
        $deletar->save();

        return 'true';
    }
}
