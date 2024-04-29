<?php

namespace App\Http\Controllers;

use DB;
use App\Preco;
use Illuminate\Http\Request;

class PrecoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            $preco = DB::table('preco')
                    ->where('id_emp', getEmpresa())
                    ->where('id_tabela_preco', $request->id_tabela_preco)
                    ->where('id_procedimento', $request->id_procedimento)
                    ->first();

            if ($preco != null) $preco = Preco::find($preco->id);
            else                $preco = new Preco;

            $preco->id_emp = getEmpresa();
            $preco->id_tabela_preco = $request->id_tabela_preco;
            $preco->id_procedimento = $request->id_procedimento;
            $preco->valor = $request->valor;
            $preco->valor_prazo = $request->valor_prazo;
            $preco->valor_minimo = $request->valor_minimo;
            $preco->save();

            return $preco;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $preco = Preco::find($request->id);
            $preco->delete();

            return $preco;
        } catch(\Exception $e) {
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

            $data->precos = DB::table('comissao_exclusiva')
                            ->select(
                                'comissao_exclusiva.id',
                                'comissao_exclusiva.id_procedimento',
                                'procedimento.id_especialidade',
                                'procedimento.descr',
                                'especialidade.descr AS descr_especialidade',
                                'comissao_exclusiva.de',
                                'comissao_exclusiva.ate',
                                'comissao_exclusiva.valor'
                            )
                            ->leftjoin('procedimento', 'procedimento.id', 'comissao_exclusiva.id_procedimento')
                            ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                            ->where('preco.id_tabela_preco', $id_tabela_precos)
                            ->orderby('procedimento.descr')
                            ->get();

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id_preco) {
        try {
            return json_encode(
                DB::table('preco')
                ->select(
                    'preco.id',
                    'preco.id_procedimento',
                    'procedimento.descr',
                    'preco.valor',
                    'preco.valor_prazo',
                    'preco.valor_minimo'
                )
                ->leftjoin('procedimento', 'procedimento.id', 'preco.id_procedimento')
                ->where('preco.id', $id_preco)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
