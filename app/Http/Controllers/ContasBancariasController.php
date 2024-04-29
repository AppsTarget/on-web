<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\ContasBancarias;
use Illuminate\Http\Request;

class ContasBancariasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $contas = DB::table('contas_bancarias')
                  ->select( 'contas_bancarias.id',
                            'contas_bancarias.numero',
                            'contas_bancarias.titular',
                            'contas_bancarias.agencia',
                            'contas_bancarias.id_banco',
                            'empresa.descr AS descr_emp')
                 ->leftjoin('empresa', 'empresa.id', 'contas_bancarias.id_emp')
                 ->where('lixeira', 'N')
                 ->get();
        $empresa = DB::table('empresa')
                   ->select('id', 'descr')
                   ->get();
        
        $caixas = DB::table('caixa')
                  ->select('caixa.id', 'caixa.descr')
                  ->where('ativo', 'S')
                  ->where('lixeira', 0)
                  ->get();
        
        return view('contas_bancarias', compact('contas', 'empresa', 'caixas'));
    }

    public function salvar(Request $request) {
        if ($request->id) $conta = ContasBancarias::find($request->id);
        else              $conta = new ContasBancarias;

        $conta->titular   = $request->titular;
        $conta->numero    = $request->conta;
        $conta->agencia   = $request->agencia;
        
        $conta->id_emp    = $request->empresa;
    
        if($request->conta_corrente  == 'true') $conta->corrente  = 'S';
        else                                    $conta->corrente  = 'N';

        if($request->conta_poupanca  == 'true') $conta->poupanca  = 'S';
        else                                    $conta->poupanca  = 'N';

        if($request->conta_aplicacao == 'true') $conta->aplicacao = 'S';
        else                                    $conta->aplicacao = 'N';

        if($request->conta_caixa == 'true'){
            $conta->caixa = 'S';
            $conta->id_banco = 0;
            $conta->id_caixa = $request->caixa;
        }
        else {
            $conta->caixa = 'N';
            $conta->id_banco  = $request->banco_id;
            $conta->id_caixa = 0;
        }
        $conta->save();
        
        return 'true';
        
    }
    
    public function editar($id) {
        $data = new \StdClass;
        $data->conta = ContasBancarias::find($id);
        $data->banco = DB::table('bancos')->where('id', $data->conta->id_banco)->first();
        return json_encode($data);
    }
    public function excluir(Request $request) {
        $conta = ContasBancarias::find($request->id);
        $conta->lixeira = 'S';
        $conta->save();

        return 'true';
    }
}
