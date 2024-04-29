<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Parametros;
use Illuminate\Http\Request;

class ParametrosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function salvar_desconto_geral(Request $request) {   
        $desconto = new Parametros;
        $desconto->id_emp = getEmpresa();
        $desconto->desconto_geral = $request->desconto;
        $desconto->save();
        return redirect('/tabela-precos');
    }
    public function mostrar_param_atual() {
        return json_encode(DB::table('parametros')
               ->where('id_emp', getEmpresa())
               ->orderBy("updated_at", 'DESC')
               ->first());
    }
}