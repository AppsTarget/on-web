<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\TabelaPrecos;
use App\Modalidades_por_plano;
use App\Comissao_exclusiva;
use App\TabelaPrecosVigencia;
use Illuminate\Http\Request;
use App\EmpresasPlano;

class TabelaPrecosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function salvar(Request $request) {
        try {
            if (!$request->id){
                $tabela_precos = new TabelaPrecos;
                $id_emp = getEmpresa();
                $tabela_precos->id_emp         = $id_emp;
                $tabela_precos->descr          = $request->descr;
                $tabela_precos->status         = $request->status;
                $tabela_precos->valor          = str_replace(',', '.', $request->valor);
                switch ($request->vigencia){
                    case 'M':
                        $tabela_precos->vigencia = 30;
                        break;
                    case 'B':
                        $tabela_precos->vigencia = 60;
                        break;
                    case 'T':
                        $tabela_precos->vigencia = 90;
                        break;
                    case 'S':
                        $tabela_precos->vigencia = 180;
                        break;
                    case 'A':
                        $tabela_precos->vigencia = 360;
                        break;
                }
                $tabela_precos->desconto_associados = str_replace(',', '.', $request->desc_associado);
                $tabela_precos->max_atv_semana = $request->max_atv_semana;
                $tabela_precos->max_atv = $request->max_atv;
                
                if ($request->repor_som_mes == true || $request->repor_som_mes == 'true'){
                    $tabela_precos->repor_som_mes  = 1;
                }
                else $tabela_precos->repor_som_mes = 0;

                if($request->usar_desconto_padrao == true || $request->usar_desconto_padrao == 'true'){
                    $tabela_precos->desconto_geral = 1;
                }
                else $tabela_precos->desconto_geral = 0;

                if($request->gerar_contrato == true || $request->gerar_contrato == 'true'){
                    $tabela_precos->contrato = 'S';
                }
                else $tabela_precos->contrato = 'N';

                switch($request->tipo_agendamento){
                    case 1:
                        $tabela_precos->pre_agendamento = 1;
                        $tabela_precos->reabilitacao = 0;
                        $tabela_precos->habilitacao = 0;
                        break;
                    case 2:
                        $tabela_precos->pre_agendamento = 0;
                        $tabela_precos->reabilitacao = 1;
                        $tabela_precos->habilitacao = 0;
                        break;
                    case 3:
                        $tabela_precos->pre_agendamento = 0;
                        $tabela_precos->reabilitacao = 0;
                        $tabela_precos->habilitacao = 1;
                        break;
                }

                $tabela_precos->save();

                foreach($request->empresas as $empresa){
                    if($empresa != 0) {
                        $empresa_plano = new EmpresasPlano;
                        $empresa_plano->id_tabela_preco = $tabela_precos->id;
                        $empresa_plano->id_emp = $empresa; 
                        $empresa_plano->save();
                    }
                }

                return $tabela_precos;
            }
            else {
                $tabela_precos = TabelaPrecos::find($request->id);
                $id_emp = getEmpresa();
                $tabela_precos->id_emp         = $id_emp;
                $tabela_precos->descr          = $request->descr;
                $tabela_precos->descr_contrato = $request->descr_contrato;
                $tabela_precos->status         = $request->status;
                $tabela_precos->valor          = str_replace(',', '.', $request->valor);
                $tabela_precos->n_pessoas      = 1;
                switch ($request->vigencia){
                    case 'M':
                        $tabela_precos->vigencia = 30;
                        break;
                    case 'B':
                        $tabela_precos->vigencia = 60;
                        break;
                    case 'T':
                        $tabela_precos->vigencia = 90;
                        break;
                    case 'S':
                        $tabela_precos->vigencia = 180;
                        break;
                    case 'A':
                        $tabela_precos->vigencia = 360;
                        break;
                }
                $tabela_precos->desconto_associados = str_replace(',', '.', $request->desc_associado);
                $tabela_precos->max_atv_semana = $request->max_atv_semana;
                $tabela_precos->max_atv = $request->max_atv;

                if ($request->repor_som_mes === true || $request->repor_som_mes === 'true'){
                    $tabela_precos->repor_som_mes  = 1;
                }else $tabela_precos->repor_som_mes = 0;

                if ($request->usar_desconto_padrao === true || $request->usar_desconto_padrao === 'true'){
                    $tabela_precos->desconto_geral = 1;
                }else $tabela_precos->desconto_geral = 0;

                if($request->gerar_contrato == true || $request->gerar_contrato == 'true'){
                    $tabela_precos->contrato = 'S';
                }
                else $tabela_precos->gerar_contrato = 'N';

                switch($request->tipo_agendamento){
                    case 1:
                        $tabela_precos->pre_agendamento = 1;
                        $tabela_precos->reabilitacao = 0;
                        $tabela_precos->habilitacao = 0;
                        break;
                    case 2:
                        $tabela_precos->pre_agendamento = 0;
                        $tabela_precos->reabilitacao = 1;
                        $tabela_precos->habilitacao = 0;
                        break;
                    case 3:
                        $tabela_precos->pre_agendamento = 0;
                        $tabela_precos->reabilitacao = 0;
                        $tabela_precos->habilitacao = 1;
                        break;
                }
                
                $tabela_precos->save();

                DB::table("empresas_plano")
                    ->where("id_tabela_preco", $tabela_precos->id)
                    ->delete();

                foreach($request->empresas as $empresa){
                    if($empresa != 0) {
                        $empresa_plano = new EmpresasPlano;
                        $empresa_plano->id_tabela_preco = $tabela_precos->id;
                        $empresa_plano->id_emp = $empresa; 
                        $empresa_plano->save();
                    }
                }

                return redirect('/tabela-precos');
            }
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function listar_modalidades($id){
        return json_encode(DB::table('modalidades_por_plano')
                            ->select('procedimento.descr',
                                     'procedimento.id')
                            ->join('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                            ->where(function($sql){
                                $sql->where('procedimento.lixeira', 0)
                                    ->orWhere('procedimento.lixeira', null);
                            })
                            ->where('modalidades_por_plano.id_tabela_preco', $id)
                            ->get());
    }
    public function deletar(Request $request) {
        try {
            $tabela_precos = TabelaPrecos::find($request->id);
            $tabela_precos->lixeira = true;
            $tabela_precos->save();

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $tabela_precos = DB::select(DB::raw("
                SELECT tabela_precos.*

                FROM tabela_precos

                JOIN (
                    SELECT id_tabela_preco

                    FROM empresas_plano

                    WHERE id_emp = ".getEmpresa()."

                    GROUP BY id_tabela_preco
                ) AS tab ON tab.id_tabela_preco = tabela_precos.id

                WHERE lixeira = 0

                ORDER BY descr
            "));
            
            $procedimentos = DB::table('procedimento')
                            ->where('id_emp', getEmpresa())
                            ->get();

            $especialidades = DB::table('especialidade')
                            ->where('id_emp', getEmpresa())
                            ->get();
            $tipo_agendamento = DB::table('tipo_procedimento')
                                ->where('id_emp', getEmpresa())
                                ->where(function($sql){
                                    $sql->where('lixeira', 0)
                                        ->orWhere('lixeira', null);
                                })
                                ->get();

            $empresas = DB::table('empresa')                    
                                ->get();


            return view('tabela_precos', compact('tabela_precos', 'procedimentos', 'especialidades', 'tipo_agendamento', 'empresas'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        // try {
            $tabela_preco = DB::table('tabela_precos')
                            // ->where('id_emp', getEmpresa())
                            ->where('id', $id)
                            // ->where('lixeira', false)
                            ->first();

            return json_encode($tabela_preco);
        // } catch (\Exception $e) {
        //     return $e->getMessage();
        // }
    }

    public function clonar_precos(Request $request) {
        try {
            $tabela_referencia = DB::table('tabela_precos')
                                ->where('id_emp', getEmpresa())
                                ->where('id', $request->id)
                                ->first();

            $modalidades_referencia = DB::table('modalidades_por_plano')
                                ->where('id_tabela_preco', $request->id)
                                ->get();

            $comissao_referencia = DB::table('comissao_exclusiva')
                                ->where('id_tabela_preco', $request->id)
                                ->get();
            
            $tabela_precos = new TabelaPrecos;
            $tabela_precos->id_emp = $tabela_referencia->id_emp;
            $tabela_precos->descr = $tabela_referencia->descr . ' (Clonado)';
            $tabela_precos->status = $tabela_referencia->status;
            $tabela_precos->valor = $tabela_referencia->valor;
            $tabela_precos->vigencia = $tabela_referencia->vigencia;
            $tabela_precos->max_atv_semana = $tabela_referencia->max_atv_semana;
            $tabela_precos->repor_som_mes = $tabela_referencia->repor_som_mes;
            $tabela_precos->save();


            foreach ($comissao_referencia as $comissao) {
                $comissao_clone = new Comissao_exclusiva;
                $comissao_clone->id_empresa      = $tabela_precos->id_emp;
                $comissao_clone->id_tabela_preco = $tabela_precos->id;
                $comissao_clone->id_procedimento = $comissao->id_procedimento;
                $comissao_clone->de2             = $comissao->de2;
                $comissao_clone->ate2            = $comissao->ate2;
                $comissao_clone->valor2          = $comissao->valor2;
                $comissao_clone->save();
            } 

            foreach ($modalidades_referencia as $modalidades){
                $modalidade_clone = new Modalidades_por_plano;
                $modalidade_clone->id_empresa      = $tabela_precos->id_emp;
                $modalidade_clone->id_tabela_preco = $tabela_precos->id;
                $modalidade_clone->id_procedimento = $modalidades->id_procedimento;
                $modalidade_clone->save();
            }

            return $tabela_precos;
        } catch(\Exception $e) {
            return $e->getMessage();
        } 
    }
    public function listar_tabela_modalidades($id_tabela_precos) {
        try {
            $data = new \StdClass;
            $data->especialidades = DB::table('especialidade')
                                    ->where('id_emp', getEmpresa())
                                    ->where('lixeira', false)
                                    ->orderby('descr')
                                    ->get();

            $data->Modalidades_por_plano = DB::table('modalidades_por_plano')
                                            ->select(
                                                'modalidades_por_plano.id',
                                                'modalidades_por_plano.id_procedimento',
                                                'procedimento.id_especialidade',
                                                'procedimento.descr',
                                                'especialidade.descr AS descr_especialidade'
                                            )
                                            ->leftjoin('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                                            ->leftjoin('especialidade', 'especialidade.id', 'procedimento.id_especialidade')
                                            ->where('modalidades_por_plano.id_tabela_preco', $id_tabela_precos)
                                            ->orderby('procedimento.descr')
                                            ->get();

            return json_encode($data);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function salvarModalidade(Request $request) {
        try {
            if (!$request->id) $modalidades_por_plano = new Modalidades_por_plano;
            else               $modalidades_por_plano = Modalidades_por_plano::find($request->id);

            $id_emp = getEmpresa();
            $modalidades_por_plano->id_empresa      = getEmpresa();
            $modalidades_por_plano->id_tabela_preco = $request->id_tabela_preco;
            $modalidades_por_plano->id_procedimento = $request->id_procedimento;
            
            $modalidades_por_plano->save();

            // return redirect('/tabela-precos');
            return $modalidades_por_plano;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function mostrarModalidade($id_modalidade) {
        try {
            return json_encode(
                DB::table('modalidades_por_plano')
                ->select(
                    'modalidades_por_plano.id',
                    'modalidades_por_plano.id_procedimento',
                    'procedimento.descr'
                )
                ->leftjoin('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                ->where('modalidades_por_plano.id', $id_modalidade)
                ->first()
            );
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletarModalidade(Request $request) {
        try {
            $modalidade = Modalidades_por_plano::find($request->id);
            $modalidade->delete();

            return $modalidade;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function procurar_modalidades($id) {
        return DB::table('modalidades_por_plano')
                ->select('procedimento.descr AS descr' , 'procedimento.id AS id')
                ->where('modalidades_por_plano.id_tabela_preco', $id)
                ->join('procedimento', 'procedimento.id', 'modalidades_por_plano.id_procedimento')
                ->get();
        
    }

    public function add_vigencia_plano(Request $request){
        $vigencia = new TabelaPrecosVigencia;
        $vigencia->id_tabela_preco = $request->id_plano;
        $vigencia->de              = $request->de;
        $vigencia->ate             = $request->ate;
        $vigencia->vigencia        = $request->vigencia;

        $vigencia->save();
    }    
    public function listar_vigencias_plano($id){
        return DB::table('tabela_precos_vigencia')
               ->where('id_tabela_preco', $id)
               ->orderBy('id', "DESC")
               ->get();
    }
    public function excluir_vigencia_plano(Request $request) {
        $vigencia = TabelaPrecosVigencia::find($request->id);
        $vigencia->delete();
        return 'true';
    }

    public function listar_empresas($id) {
        $data = new \StdClass;

        $data->empresa_plano = DB::table('empresas_plano')
                               ->select('empresa.id', 'empresa.descr')
                               ->leftjoin('empresa', 'empresa.id', 'empresas_plano.id_emp')
                               ->where('empresas_plano.id_tabela_preco', $id)
                                ->get();
            
        $data->empresas = DB::table('empresa')
                          ->get();
                          
        return json_encode($data);
    }
}
