<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\TabelaPrecos;
use App\Comissao_exclusiva;
use Illuminate\Http\Request;

class Comissao_exclusivaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $Comissao_exclusiva = new Comissao_exclusiva;
            else               $Comissao_exclusiva = Comissao_exclusiva::find($request->id);

            $id_emp = getEmpresa();
            $Comissao_exclusiva->id_empresa      = $id_emp;
            $Comissao_exclusiva->id_tabela_preco = $request->id_tabela_preco;
            $Comissao_exclusiva->id_procedimento = $request->id_procedimento;
            $Comissao_exclusiva->de2             = $request->de2;
            $Comissao_exclusiva->ate2            = $request->ate2;
            $Comissao_exclusiva->valor2          = $request->valor2;
            
            $Comissao_exclusiva->save();

            // return redirect('/tabela-precos');
            return $Comissao_exclusiva;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function listar_tabela($id_tabela_precos) {
        try {
            $data = new \StdClass;
            $data->especialidades = DB::table('especialidade')
                                    ->where('id_emp', getEmpresa())
                                    ->where('lixeira', false)
                                    ->orderby('descr')
                                    ->get();

            $data->Comissao_exclusiva = DB::table('comissao_exclusiva')
                            ->select(
                                'comissao_exclusiva.id',
                                'comissao_exclusiva.id_procedimento',
                                'procedimento.id_especialidade',
                                'procedimento.descr',
                                'especialidade.descr AS descr_especialidade',
                                'comissao_exclusiva.de2',
                                'comissao_exclusiva.ate2',
                                'comissao_exclusiva.valor2'
                            )
                            ->leftjoin('procedimento', 'procedimento.id', 'comissao_exclusiva.id_procedimento')
                            ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                            ->where('comissao_exclusiva.id_tabela_preco', $id_tabela_precos)
                            ->orderby('procedimento.descr')
                            ->get();

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $comissao = Comissao_exclusiva::find($request->id);
            $comissao->delete();

            return $comissao;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $tabela_precos = DB::table('tabela_precos')
                            ->where('id_emp', getEmpresa())
                            ->get();

            $procedimentos = DB::table('procedimento')
                            ->where('id_emp', getEmpresa())
                            ->get();

            $especialidades = DB::table('especialidade')
                            ->where('id_emp', getEmpresa())
                            ->get();

            return view('tabela_precos', compact('tabela_precos', 'procedimentos', 'especialidades'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id_preco) {
        try {
            return json_encode(
                DB::table('comissao_exclusiva')
                ->select(
                    'comissao_exclusiva.id',
                    'comissao_exclusiva.id_procedimento',
                    'procedimento.descr',
                    'comissao_exclusiva.de2',
                    'comissao_exclusiva.ate2',
                    'comissao_exclusiva.valor2'
                )
                ->leftjoin('procedimento', 'procedimento.id', 'comissao_exclusiva.id_procedimento')
                ->where('comissao_exclusiva.id', $id_preco)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function clonar_precos(Request $request) {
        try {
            $tabela_referencia = DB::table('tabela_precos')
                                ->where('id_emp', getEmpresa())
                                ->where('id', $request->id)
                                ->first();
            
            $tabela_precos = new TabelaPrecos;
            $tabela_precos->id_emp = $tabela_referencia->id_emp;
            $tabela_precos->descr = $tabela_referencia->descr . ' (Clonado)';
            $tabela_precos->status = $tabela_referencia->status;
            $tabela_precos->save();

            $precos_referencia = DB::table('preco')
                                ->where('id_tabela_preco', $request->id)
                                ->get();

            foreach ($precos_referencia as $preco) {
                $preco_clone = new Preco;
                $preco_clone->id_emp = $preco->id_emp;
                $preco_clone->id_tabela_preco = $tabela_precos->id;
                $preco_clone->id_procedimento = $preco->id_procedimento;
                $preco_clone->valor = $preco->valor;
                $preco_clone->save();
            } 

            return $tabela_precos->id;
        } catch(\Exception $e) {
            return $e->getMessage();
        } 
    }
}
