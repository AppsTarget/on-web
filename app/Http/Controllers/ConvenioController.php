<?php

namespace App\Http\Controllers;

use App\Convenio;
use App\PrecoConvenioPlanos;
use DB;
use Illuminate\Http\Request;

class ConvenioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request)
    {
        try {
            if ($request->id) $convenio = Convenio::find($request->id);
            else              $convenio = new Convenio;
            $convenio->id_emp = getEmpresa();
            $convenio->descr = $request->descr;
            $convenio->prazo = $request->prazo;
            if ($request->quem_paga == true){
                $convenio->quem_paga = 'E';
            }
            else {
                $convenio->quem_paga = 'C';
                $convenio->id_pessoa = $request->id_pessoa;
            }
            $convenio->save();

            return redirect('/convenio');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function inativar(Request $request)
    {
        try {
            $convenio = Convenio::find($request->id_convenio);
            $convenio->lixeira = true;
            $convenio->updated_at = date('Y-m-d H:i:s');
            $convenio->save();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id)
    {
        try {
            $data = new \StdClass;
            $data->convenio = DB::table('convenio')
                            ->select(
                                'convenio.id',
                                'convenio.descr',
                                'convenio.id_tabela_preco',
                                'pessoa.nome_fantasia AS cliente_nome',
                                'convenio.id_pessoa',
                                'convenio.quem_paga',
                                'convenio.prazo'
                            )
                            ->leftjoin('pessoa', 'pessoa.id', 'convenio.id_pessoa')
                            ->where('convenio.id', $id)
                            ->first();
            
            $data->precos_por_convenio = DB::table("preco_convenios_plano")
                                        ->select('preco_convenios_plano.id as id',
                                                 'tabela_precos.descr as descr',
                                                 'empresa.descr as descr_empresa',
                                                 'preco_convenios_plano.valor as valor')
                                        ->join('tabela_precos', 'tabela_precos.id', 'preco_convenios_plano.id_tabela_preco')
                                        ->join('empresa', 'empresa.id', 'preco_convenios_plano.id_emp')
                                        ->where('preco_convenios_plano.id_convenio', $id)
                                        ->where(function($sql) {
                                            $sql->where('preco_convenios_plano.lixeira', 0)
                                                ->orWhere('preco_convenios_plano.lixeira', null);
                                        })
                                        ->get();
            return json_encode($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar()
    {
        try {
            $convenios = DB::table('convenio')
                ->select(
                    'convenio.id',
                    'convenio.descr',
                    'tabela_precos.descr AS tabela_preco_descr',
                    'pessoa.nome_fantasia AS cliente_nome'
                )
                ->leftjoin('tabela_precos', 'tabela_precos.id', 'convenio.id_tabela_preco')
                ->leftjoin('pessoa', 'pessoa.id', 'convenio.id_pessoa')
                ->where('convenio.id_emp', getEmpresa())
                ->where('convenio.lixeira', false)
                ->get();

            $tabela_precos = DB::table('tabela_precos')
                            ->select("tabela_precos.id as id",
                                        DB::raw("CASE WHEN (tabela_precos.vigencia = 30) THEN (" . 
                                                         "CONCAT(tabela_precos.descr,' | mensal | R$ ', tabela_precos.valor))".
                                                     "WHEN (tabela_precos.vigencia = 60) THEN (".
                                                         "CONCAT(tabela_precos.descr, ' | bimestral | R$ ', tabela_precos.valor))".
                                                     "WHEN (tabela_precos.vigencia = 90) THEN (".
                                                         "CONCAT(tabela_precos.descr, ' | trimestral | R$ ', tabela_precos.valor))".
                                                     "WHEN (tabela_precos.vigencia = 180) THEN (".
                                                         "CONCAT(tabela_precos.descr, ' | semestral | R$ ', tabela_precos.valor))".
                                                     "WHEN (tabela_precos.vigencia = 360) THEN (".
                                                         "CONCAT(tabela_precos.descr, ' | anual | R$ ', tabela_precos.valor))".
                                                         " ELSE '' END AS descr"))
                            ->where('id_emp', getEmpresa())
                            ->get();
            $empresas = DB::table('empresa')
                        ->selectRaw("empresa.id, 
                                     CONCAT(empresa.descr, ' - ', empresa.cidade, ' - ', empresa.uf) as descr")
                        ->get();
            return view('convenio', compact('convenios', 'tabela_precos', 'empresas'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('convenio')
                ->select(
                    'convenio.id',
                    'convenio.descr',
                    'tabela_precos.descr AS tabela_preco_descr'
                )
                ->leftjoin('tabela_precos', 'tabela_precos.id', 'convenio.id_tabela_preco')
                ->where('convenio.id_emp', getEmpresa())
                ->where('convenio.lixeira', false)
                ->get()
            );

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function criar_convenio(Request $request) {
        try {
            if ($request->id) $convenio = Convenio::find($request->id);
            else $convenio = new Convenio;
            $convenio->id_emp = getEmpresa();
            $convenio->descr = $request->descr;
            $convenio->prazo = $request->prazo;
            if ($request->quem_paga == true){
                $convenio->quem_paga = 'E';
            }
            else {
                $convenio->quem_paga = 'C';
                $convenio->id_pessoa = $request->id_pessoa;
            }
            $convenio->save();
            if ($convenio->quem_paga == 'E') return $convenio;
            else                             return DB::table('convenio')
                                                    ->join("pessoa", "pessoa.id", 'convenio.id_pessoa')
                                                    ->where('convenio.id', $convenio->id)
                                                    ->first();
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
    public function adicionar_valor_por_plano(Request $request){
        try{
            $valor_por_plano = new PrecoConvenioPlanos;

            $valor_por_plano->id_emp          = $request->id_emp;
            $valor_por_plano->id_tabela_preco = $request->id_tabela_preco;
            $valor_por_plano->id_convenio     = $request->id_convenio;
            $valor_por_plano->valor           = $request->valor;
            $valor_por_plano->save();

            return json_encode(DB::table("preco_convenios_plano")
                   ->select('preco_convenios_plano.id as id',
                            'tabela_precos.descr as descr',
                            'empresa.descr as descr_empresa',
                            'preco_convenios_plano.valor as valor')
                   ->join('tabela_precos', 'tabela_precos.id', 'preco_convenios_plano.id_tabela_preco')
                   ->join('empresa', 'empresa.id', 'preco_convenios_plano.id_emp')
                   ->where("preco_convenios_plano.id_convenio", $request->id_convenio)
                   ->where(function($sql){
                        $sql->where('preco_convenios_plano.lixeira', null)
                            ->orWhere('preco_convenios_plano.lixeira', 0);
                   })
                   ->orderBy('preco_convenios_plano.created_at', 'DESC')
                   ->get());

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function remover_preco_convenio(Request $request){
        $valor_por_plano = PrecoConvenioPlanos::find($request->id);
        $valor_por_plano->lixeira = true;
        $valor_por_plano->save();
        
        return json_encode(DB::table("preco_convenios_plano")
                            ->select('preco_convenios_plano.id as id',
                                    'tabela_precos.descr as descr',
                                    'empresa.descr as descr_empresa',
                                    'preco_convenios_plano.valor as valor')
                            ->join('tabela_precos', 'tabela_precos.id', 'preco_convenios_plano.id_tabela_preco')
                            ->join('empresa', 'empresa.id', 'preco_convenios_plano.id_emp')
                            ->where("preco_convenios_plano.id_convenio", PrecoConvenioPlanos::find($request->id)->id_convenio)
                            ->where(function($sql){
                                $sql->where('preco_convenios_plano.lixeira', null)
                                    ->orWhere('preco_convenios_plano.lixeira', 0);
                            })
                            ->orderBy('preco_convenios_plano.created_at', 'DESC')
                            ->get());

    }
    public function verificar_carteira(Request $request){
        $carteiras = DB::table("convenio_pessoa")
                    ->where('id_paciente', $request->id_paciente)
                    ->where('id_convenio', $request->id_convenio)
                    ->first();
        if (trim($carteiras->num_convenio) === trim($request->num_carteira)) return 'true';
        else                                                     return 'false';
    }
}
