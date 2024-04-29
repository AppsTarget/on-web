<?php

namespace App\Http\Controllers;

use DB;
use App\Empresa;
use App\EmpresaResponsaveis;
use Illuminate\Http\Request;

class EmpresaController extends Controller {
    public function criarEmpresa(Request $request) {
        $empresa = new Empresa;
        $empresa->descr = $request->descr;
        $empresa->endereco = $request->endereco;
        $empresa->telefone = $request->telefone;
        $empresa->save();
        $empresa_responsaveis = new EmpresaResponsaveis;
        $empresa_responsaveis->id_emp = $empresa->id;
        $empresa_responsaveis->id_responsavel = $request->id_responsavel;
        $empresa_responsaveis->save();
        return redirect("cadastro-de-empresa");
    }

    public function listarEmpresas() {
        $empresas = DB::table('empresa')
                        ->join('empresa_responsaveis', 'empresa.id', '=', 'empresa_responsaveis.id_emp')
                        ->join('pessoa', 'pessoa.id', '=', 'empresa_responsaveis.id_responsavel')
                        ->select('empresa.id as id',
                                'empresa.mod_cod_interno as codigo',
                                'empresa.descr as descr',
                                'empresa.telefone as tel_empresa',
                                'pessoa.celular1 as tel_responsavel',
                                'pessoa.nome_fantasia as responsavel')
                        ->where('empresa_responsaveis.lixeira', 0)
                        ->where('empresa.dump', 0)
                        ->get();
        return view("cadastro_de_empresa", compact("empresas"));
    }

    public function verEmpresa(Request $request) {
        return DB::table('empresa')
                   ->join('empresa_responsaveis', 'empresa.id', '=', 'empresa_responsaveis.id_emp')
                   ->join('pessoa', 'pessoa.id', '=', 'empresa_responsaveis.id_responsavel')
                   ->select('empresa.id as id',
                           'empresa.descr as descr',
                           'empresa.telefone as telefone',
                           'empresa.endereco as endereco',
                           'pessoa.nome_fantasia as responsavel',
                           'empresa_responsaveis.id_responsavel as id_responsavel',
                           'empresa_responsaveis.id as id_enc')
                   ->where('empresa.id', $request->id_emp)
                   ->where('empresa_responsaveis.lixeira', 0)
                   ->where('empresa.dump', 0)
                   ->get();
    }

    public function editarEmpresa(Request $request) {
        $empresa = Empresa::find($request->id_empresa);
        $empresa->descr = $request->descr;
        $empresa->endereco = $request->endereco;
        $empresa->telefone = $request->telefone;
        $empresa->save();
        $empresa_responsaveis = EmpresaResponsaveis::find($request->id_enc);
        $empresa_responsaveis->id_emp = $request->id_empresa;
        $empresa_responsaveis->id_responsavel = $request->id_responsavel;
        $empresa_responsaveis->save();
        return redirect("cadastro-de-empresa");
    }

    public function deletarEmpresa(Request $request) {
        $data = Empresa::find($request->id_emp);
        $data->dump = 1;
        $data->save();
        return;
    }
}