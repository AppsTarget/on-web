<?php

namespace App\Http\Controllers;

use DB;
use App\Procedimento;
use App\Comissao_exclusiva;
use Illuminate\Http\Request;

class ProcedimentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listar() {
        $procedimentos = DB::table('procedimento')
                        ->select(
                            'procedimento.id',
                            'procedimento.id_especialidade',
                            'procedimento.cod_tuss',
                            'procedimento.descr',
                            'procedimento.descr_resumida',
                            'especialidade.descr AS descr_especialidade',
                            'procedimento.tempo_procedimento'
                        )
                        ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                        // ->where('procedimento.id_emp', getEmpresa())
                        ->where(function($sql){
                            $sql->where('oculto', 0)
                                ->orWhere('oculto', null);
                        })
                        ->orderby('procedimento.descr')
                        ->get();

        $especialidades = DB::table('especialidade')
                        // ->where('id_emp', getEmpresa())
                        ->where('lixeira', false)
                        ->orderby('descr')
                        ->get();

        return view('procedimento', compact('procedimentos', 'especialidades'));
    }

    public function salvar(Request $request) {
        try {
            if ($request->id) $procedimento = procedimento::find($request->id);
            else              $procedimento = new procedimento;

                $procedimento->id_emp = getEmpresa();
                $procedimento->id_especialidade = $request->especialidade;
                $procedimento->cod_tuss = $request->cod_tuss;
                $procedimento->tempo_procedimento = $request->tempo_procedimento;
                $procedimento->descr = $request->descr;
                $procedimento->descr_resumida = $request->descr_resumida;
                $procedimento->obs = $request->obs;
                $procedimento->faturar = $request->faturar;

                $procedimento->save();
                return $procedimento;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $procedimento = procedimento::find($request->id_procedimento);
            $procedimento->lixeira = true;
            $procedimento->save();

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('procedimento')
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function listar_metas($id){
        return json_encode(DB::table('comissao_exclusiva')
                            // ->where('id_empresa', getEmpresa())
                            ->where('id_procedimento', $id)
                            ->orderBy('id', 'DESC')
                            ->get());
    }
    public function excluir_meta(Request $request){
        Comissao_exclusiva::find($request->id_meta)->delete();
        return 'true';
    }
    public function adicionar_metas(Request $request){
        try {
            $metas = new Comissao_exclusiva;
            // $metas->id_empresa = getEmpresa();
            $metas->id_procedimento = $request->id_procedimento;
            $metas->de2 = $request->de;
            $metas->ate2 = $request->ate;
            $metas->valor2 = $request->valor;
            $metas->save();
            return "true";

        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
    public function listar_json() {
        try {
            return json_encode(
                DB::table('procedimento')
                ->where(function($sql){
                    $sql->where('lixeira', false)
                        ->orWhere('lixeira', 'is', null);
                })
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function verificar_convenio(Request $request) {
        try {
            return json_encode(
                DB::table('preco')
                ->select(
                    'preco.id',
                    'preco.valor',
                    'preco.valor_prazo',
                    'preco.valor_minimo',
                    'procedimento.dente_regiao',
                    'procedimento.face'
                )
                ->join('procedimento', 'procedimento.id', 'preco.id_procedimento')
                ->join('tabela_precos', 'tabela_precos.id', 'preco.id_tabela_preco')
                ->join('convenio', 'convenio.id_tabela_preco', 'tabela_precos.id')
                ->where('tabela_precos.status', 'A')
                ->where('preco.id_procedimento', $request->id_procedimento)
                ->where('convenio.id', $request->id_convenio)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function salvar_metas_modalidade(Request $request){
        $procedimento = Procedimento::find($request->id);
        $procedimento->valor_total = $request->valor_total;
        $procedimento->tipo_de_comissao = $request->tipo_de_comissao;
        $procedimento->total_agendamentos_meta = $request->total_agendamentos_metas;
        $procedimento->save();
        return 'true';
    }
}
