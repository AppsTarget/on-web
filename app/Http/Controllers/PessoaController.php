<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Hash;
use App\User;
use App\Users;
use App\UsersApp;
use App\Pessoa;
use App\Historico;
use App\EmpresasProfissional;
use App\ConvenioPessoa;
use App\EspecialidadePessoa;
use App\EncaminhantesEspecialidade;
use App\AssociadosRegra;
use App\Agenda;
use App\Encaminhantes;
use App\Http\Controllers\ReceitaController;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function max_cod_interno() {
        $cod_interno =  DB::table('pessoa')->where('id_emp', getEmpresa())->max('cod_interno');
        if ($cod_interno == null || $cod_interno == 0) $cod_interno = 1;
        else                                           $cod_interno++;
        return $cod_interno;
    }

    public function salvar(Request $request)
    {
        try {
            if ($request->id == null) $pessoa = new Pessoa;
            else                      $pessoa = Pessoa::find($request->id);
            // return 'aipsdpoasmdpsamda';
            if ($request->isPaciente == 'on') {
                if (getEmpresaObj()->mod_cod_interno) $pessoa->cod_interno = $request->cod_interno;
                $pessoa->paciente = 'S';
                $pessoa->resp_nome = $request->resp_nome;
                $pessoa->resp_grau_parente = $request->resp_grau_parente;
                $pessoa->resp_cpf = $request->resp_cpf;
                $pessoa->resp_rg = $request->resp_rg;
                $pessoa->resp_celular = $request->resp_celular;
                $pessoa->psq = $request->psq;
                if ($request->resp_localizacao == 'on') {
                    $pessoa->resp_cep = $request->resp_cep;
                    $pessoa->resp_cidade = $request->resp_cidade;
                    $pessoa->resp_uf = $request->resp_uf;
                    $pessoa->resp_endereco = $request->resp_endereco;
                    $pessoa->resp_numero = $request->resp_numero;
                    $pessoa->resp_bairro = $request->resp_bairro;
                    $pessoa->resp_complemento = $request->resp_complemento;
                } else {
                    $pessoa->resp_cep = '';
                    $pessoa->resp_cidade = '';
                    $pessoa->resp_uf = '';
                    $pessoa->resp_endereco = '';
                    $pessoa->resp_numero = '';
                    $pessoa->resp_bairro = '';
                    $pessoa->resp_complemento = '';
                }
            }

            if ($request->isCliente == 'on') $pessoa->cliente = 'S';
            else                              $pessoa->cliente = 'N';
            if ($request->isMedico   == 'on') $pessoa->colaborador = 'P';
            else {
                $pessoa->colaborador = "N";
                $pessoa->cliente = "N";
                $pessoa->administrador = "N";
                $pessoa->fornecedor = "N";
            }
            if ($request->isRecepcao == 'on') $pessoa->colaborador = 'R';
            if ($request->isRecepcao == 'on' and $request->isMedico == 'on'){
                $pessoa->colaborador = 'A';
            }
            if ($request->isMedico == 'on' || $request->isRecepcao == 'on') {
                $pessoa->fornecedor = 'S';
                if ($request->isAdministrador == 'on') $pessoa->administrador = 'S';
            }

            //return getEmpresa();
            $pessoa->id_emp = getEmpresa();
            $pessoa->nome_fantasia = $request->nome_fantasia;
            $pessoa->nome_reduzido = $request->nome_reduzido;
            $pessoa->email = $request->email;

            if ($request->tpessoa == 'on') {
                $pessoa->tpessoa = 'J';
                $pessoa->razao_social = $request->razao_social;
                $pessoa->sexo = '';
                $pessoa->cpf_cnpj = $request->cnpj;
                $pessoa->rg_ie = $request->ie;
                $pessoa->estado_civil = '';
                $pessoa->data_nasc = '';
            } else {
                $pessoa->tpessoa = 'F';
                $pessoa->razao_social = '';
                $pessoa->sexo = $request->sexo;
                $pessoa->cpf_cnpj = $request->cpf;
                $pessoa->rg_ie = $request->rg;
                $pessoa->estado_civil = $request->estado_civil;
                if ($request->data_nasc != '') $pessoa->data_nasc = implode('-', array_reverse(explode('/', $request->data_nasc)));
                $pessoa->profissao = $request->profissao;
            }

            // if (getEmpresaObj()->tipo != 'D') {
            //     $pessoa->altura = $request->altura;
            //     $pessoa->peso = $request->peso;
            // }

            $pessoa->crm_cro = $request->crm_cro;
            $pessoa->crm = $request->crm;
            $pessoa->uf_crm = $request->uf_crm;
            $pessoa->cref = $request->cref;
            $pessoa->uf_cref = $request->uf_cref;
            $pessoa->creft = $request->creft;
            $pessoa->uf_creft = $request->uf_creft;
            $pessoa->crn = $request->crn;
            $pessoa->uf_crn = $request->uf_crn;
            $pessoa->telefone1 = $request->telefone1;
            $pessoa->telefone2 = $request->telefone2;
            $pessoa->celular1 = $request->celular1;
            $pessoa->celular2 = $request->celular2;
            $pessoa->cep = $request->cep;
            $pessoa->endereco = $request->endereco;
            $pessoa->numero = $request->numero;
            $pessoa->complemento = $request->complemento;
            $pessoa->bairro = $request->bairro;
            $pessoa->cidade = $request->cidade;
            $pessoa->uf = $request->uf;
            $pessoa->obs = $request->obs;

            // return $request->nao_gerar_faturamento;

            if ($request->nao_gerar_faturamento === 'N') {
                $pessoa->gera_faturamento = 'N';
                $pessoa->d_naofaturar = implode('-', array_reverse(explode('/', $request->data)));
                $pessoa->aplicar_desconto = $request->aplicar_desconto;
            }
            else {
                $pessoa->gera_faturamento = 'S';
                $pessoa->aplicar_desconto = 'S';
            }

            $pessoa->save();

            //Lucas
            $lista_historico = DB::table('historico')->where('id_pessoa', $pessoa->id)->orderBy('id','DESC')->get();
            $hist_exist = sizeof($lista_historico);
            $prefix = "paciente";
            if ($hist_exist) {
                $ultimo_registro = $lista_historico[0]->acao;
                if($ultimo_registro == 'S' && $pessoa->colaborador <> 'N'){
                    $historico = new Historico;
                    $historico->id_pessoa = $pessoa->id;
                    $historico->acao = 'E';
                    $prefix = "profissional/E";
                    $historico->created_by = Auth::user()->id_profissional;
                    $historico->save();
                } else if($ultimo_registro == 'E' && $pessoa->colaborador == 'N'){
                    $historico = new Historico;
                    $historico->id_pessoa = $pessoa->id;
                    $historico->acao = 'S';
                    $prefix = "profissional/S";
                    $historico->created_by = Auth::user()->id_profissional;
                    $historico->save();
                }
            } else if($pessoa->colaborador <> 'N'){
                $historico = new Historico;
                $historico->id_pessoa = $pessoa->id;
                $historico->acao = 'E';
                $historico->created_by = Auth::user()->id_profissional;
                $historico->save();
                $prefix = "profissional/E";
            }


            if ($request->isMedico == "on") {
                $encaminhante = new Encaminhantes;
                $encaminhante->id_pessoa = $pessoa->id;
                $encaminhante->nome_fantasia = $pessoa->nome_fantasia;
                if ($pessoa->celular1 != null) $encaminhante->telefone = $pessoa->celular1;
                else if ($pessoa->celular2 != null) $encaminhante->telefone = $pessoa->celular2;
                else if ($pessoa->telefone1 != null) $encaminhante->telefone = $pessoa->telefone1;
                else $encaminhante->telefone = $pessoa->telefone2;
                if ($pessoa->crm != null && $pessoa->uf_crm != null) {
                    $encaminhante->documento = $pessoa->crm;
                    $encaminhante->documento_estado = $pessoa->uf_crm;
                    $encaminhante->tpdoc = "crm";
                } else if ($pessoa->cref != null && $pessoa->uf_cref != null) {
                    $encaminhante->documento = $pessoa->cref;
                    $encaminhante->documento_estado = $pessoa->uf_cref;
                    $encaminhante->tpdoc = "cref";
                } else if ($pessoa->creft != null && $pessoa->uf_creft != null) {
                    $encaminhante->documento = $pessoa->creft;
                    $encaminhante->documento_estado = $pessoa->uf_creft;
                    $encaminhante->tpdoc = "creft";
                } else if ($pessoa->crn != null && $pessoa->uf_crn != null) {
                    $encaminhante->documento = $pessoa->crn;
                    $encaminhante->documento_estado = $pessoa->uf_crn;
                    $encaminhante->tpdoc = "crn";
                } else if ($pessoa->rg_ie != null) {
                    $encaminhante->documento = $pessoa->rg_ie;
                    $encaminhante->tpdoc = "rg_ie";
                } else if ($pessoa->cpf_cnpj != null) {
                    $encaminhante->documento = $pessoa->cpf_cnpj;
                    $encaminhante->tpdoc = "cpf_cnpj";
                }
                $encaminhante->save();
            }
                
            if ($request->isMedico == 'on' || $request->isRecepcao == 'on' || $request->isAdministrador == 'on') {
                if ($request->id == null) $this->salvarUsuario($request->nome_fantasia, $request->email, $pessoa->id,  $request->password);
                else {
                    $aux = DB::table('users')
                        ->where('id_profissional', $request->id)
                        ->count();
                    if ($aux > 0) {
                        $this->atualizarUsuario($request->nome_fantasia, $request->email, $request->id,  $request->password);
                    }
                    else $this->salvarUsuario($request->nome_fantasia, $request->email, $pessoa->id,  $request->password);
                }
            }

            if ($request->file('foto') != null) {
                $path = public_path('img') . '/pessoa/' . getEmpresa() . '/';
                \File::makeDirectory($path, $mode = 0777, true, true);

                $request->file('foto')->move($path, $pessoa->id . '.jpg');
            } else {
                print_r('Arquivo inválido');
            }

            if ($request->isCliente !== 'on') {
                DB::table('convenio_pessoa')->where('id_paciente', $pessoa->id)->delete();
                foreach ($request->convenio as $index => $convenio_id) {
                    if ($convenio_id != 0) {
                        $convenio = DB::table('convenio_pessoa')
                                ->where('id_paciente', $pessoa->id)
                                ->where('id_convenio', $convenio_id)
                                ->first();

                        if ($convenio != null) $convenio = ConvenioPessoa::find($convenio->id);
                        else                   $convenio = new ConvenioPessoa;

                        $convenio->id_paciente = $pessoa->id;
                        $convenio->id_convenio = $convenio_id;
                        $convenio->num_convenio = $request->num_convenio[$index];
                        $convenio->save();
                    }
                }
            }
            if ($request->especialidade){
                if (sizeof($request->especialidade) > 0) {
                    DB::table('especialidade_pessoa')
                    ->where('id_profissional', $pessoa->id)
                    ->delete();
                }
            }
            if ($request->empresa > 0){
                if (sizeof($request->empresa) > 0) {
                    DB::table('empresas_profissional')
                    ->where('id_profissional', $pessoa->id)
                    ->delete();
                }
            }
            try {
                if ($request->empresa) {
                    foreach($request->empresa As $empresa_id) {
                        $empresa = new EmpresasProfissional;    
                        $empresa->id_profissional = $pessoa->id;
                        $empresa->id_emp = $empresa_id;
                        $empresa->save();
                    }
                }    
            } catch (\Exception $e) { }

            if ($request->isMedico   == 'on' && $request->especialidade) {
                $encaminhante = DB::table("enc2_encaminhantes")->where('id_pessoa', $pessoa->id)->value('id');
                DB::table("enc2_encaminhantes_especialidade")
                    ->where("id_encaminhante", $encaminhante)
                    ->delete();
                foreach ($request->especialidade as $especialidade_id) {
                    if ($especialidade_id != 0) {
                        $especialidade = new especialidadePessoa;

                        $especialidade->id_profissional  = $pessoa->id;
                        $especialidade->id_especialidade = $especialidade_id;
                        $especialidade->save();
                        
                        $especialidade = new EncaminhantesEspecialidade;
                        $especialidade->id_encaminhante = $encaminhante;
                        $especialidade->id_especialidade = $especialidade_id;
                        $especialidade->save();
                    }
                }
            }

            if ($request->senha_app) {
                $aux = DB::table('usersApp')
                       ->where('id_pessoa', $pessoa->id)
                       ->first();
                if ($aux) $userApp = UsersApp::find($aux->id);
                else      $userApp = new UsersApp;
                $userApp->id_emp = getEmpresa();
                $userApp->id_pessoa = $pessoa->id;
                $userApp->email = $pessoa->email;
                $userApp->senha = $request->senha_app;
                $userApp->save();
            }
            // if(strpos(request()->headers->get('referer'), '/pessoa/prontuario/') !== false) {
            //     return redirect(request()->headers->get('referer'));
            // } else if(strpos(request()->headers->get('referer'), '/agenda') !== false) {
            //     return redirect(request()->headers->get('referer'));
            // } else if(strpos(request()->headers->get('refer'), '/pedido') !== false) {
            //     echo '<script>$('+ '</script>';
            // }else {
            //     return redirect($prefix);
            // }
            if(strpos(request()->headers->get('referer'), '/pessoa/prontuario/') !== false) {
                return redirect(request()->headers->get('referer'));
            } else if(strpos(request()->headers->get('referer'), '/agenda') !== false) {
                return redirect(request()->headers->get('referer'));
            } else {
                return redirect($prefix);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function verificar_adm(){
        if (Pessoa::find(Auth::user()->id_profissional)->administrador == 'S'){
            return 'true';
        }else return 'false';
    }
    public function verificar_adm_agenda($id){
        $agendamento = Agenda::find($id);
        if($agendamento) {
            $ar = array();
            if ($agendamento->status == 'F'){
                array_push($ar, 'S');
            }
            else {
                array_push($ar, 'N');
            }
            array_push($ar, Pessoa::find(Auth::user()->id_profissional)->administrador);
            array_push($ar, Pessoa::find(Auth::user()->id_profissional)->colaborador);
            return $ar;   
        }
        else {
            $agendamento = DB::table('old_mov_atividades')
                            ->where('id', $id)->first();
            $ar = array();
            if ($agendamento->status == 'F'){
                array_push($ar, 'S');
            }
            else {
                array_push($ar, 'N');
            }
            array_push($ar, Pessoa::find(Auth::user()->id_profissional)->administrador);
            array_push($ar, Pessoa::find(Auth::user()->id_profissional)->colaborador);
            return $ar;
        }
    }
    public function verificar_adm_agenda2(){
        $ar = array();
        array_push($ar, 0);
        array_push($ar, Pessoa::find(Auth::user()->id_profissional)->administrador);
        array_push($ar, Pessoa::find(Auth::user()->id_profissional)->colaborador);
        return $ar;   
    }

    private function salvarUsuario($name, $email, $id_profissional, $password)
    {
        $user = new User;
        $user->id_emp = getEmpresa();
        $user->id_profissional = $id_profissional;
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
    }

    private function atualizarUsuario($name, $email, $id_profissional, $password)
    {
        if ($password != '') {
            return DB::table('users')
                ->where('id_profissional', $id_profissional)
                ->update([
                    'email' => $email,
                    'name' => $name,
                    'password' => Hash::make($password),
                ]);
        } else {
            return DB::table('users')
                ->where('id_profissional', $id_profissional)
                ->update([
                    'email' => $email,
                    'name' => $name
                ]);
        }
    }

    public function inativar(Request $request) {
        try {
            $pessoa = Pessoa::find($request->id_pessoa);
            $pessoa->lixeira = true;
            $pessoa->updated_at = date('Y-m-d H:i:s');
            $pessoa->data_lixeira = date('Y-m-d H:i:s');
            $pessoa->save();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id_pessoa) {
        try {
            $pessoa =  DB::table('pessoa')
                        ->where('id', $id_pessoa)
                        ->first();

            $pessoa->convenio_pessoa = DB::table('convenio_pessoa')
                                        ->select(
                                            'convenio_pessoa.*',
                                            'convenio.descr AS descr_convenio'
                                        )
                                        ->leftjoin('convenio', 'convenio.id', 'convenio_pessoa.id_convenio')
                                        ->where('id_paciente', $id_pessoa)
                                        ->orderby(DB::raw("FIELD(convenio.quem_paga, 'E', 'C', '')"))
                                        ->get();

            $pessoa->convenios = DB::table('convenio')
                                // ->where('id_emp', getEmpresa())
                                ->orderby('quem_paga', 'DESC')
                                ->orderby('descr')
                                ->get();

            $pessoa->especialidade_pessoa = DB::select(DB::raw("
                SELECT
                    MAX(id) AS id,
                    id_profissional,
                    id_especialidade

                FROM especialidade_pessoa

                WHERE id_profissional = ".$id_pessoa."

                GROUP BY id_profissional, id_especialidade
            "));
                
            $pessoa->empresa_pessoa = DB::table('empresas_profissional')
                                      ->select('empresa.id', 'empresa.descr')
                                      ->where('id_profissional', $id_pessoa)
                                      ->leftjoin('empresa', 'empresa.id', 'empresas_profissional.id_emp')
                                      ->get();

            $pessoa->empresas = DB::table('empresa')
                               ->get();

            $pessoa->especialidades = DB::table('especialidade')
                                // ->where('id_emp', getEmpresa())
                                ->get();

            return json_encode($pessoa);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function abrir_prontuario($id_pessoa) {
        $query = DB::select(DB::raw($this->queryPaciente($id_pessoa, "")));
        $pessoa = $query[0];
        if ($pessoa->data_nasc != null && $pessoa->data_nasc != 'null' && $pessoa->data_nasc != ''){
            try {
                $data1 = new \DateTime(date($pessoa->data_nasc));
                $data2 = new \DateTime(date('Y-m-d'));
        
                $intervalo = $data1->diff($data2);
                $idade = getAge($pessoa->data_nasc);    
            } catch(\Exception $e) {
                $idade = "Não Informado";
            }
        }
        else {
            $idade = "Não Informado";
        }

        $primeira_consulta = DB::table('agenda')
                                ->where('id_paciente', $id_pessoa)
                                ->orderby('created_at')
                                ->value('data');

        $tabela_precos = DB::table('tabela_precos')
                                // ->where('id_emp', getEmpresa())
                                ->get();
        $especialidades = DB::table('especialidade')
                        //   ->where('id_emp', getEmpresa())
                          ->get();
        $especialidades_ = DB::table('especialidade_pessoa')
                           ->select('especialidade.id', 'especialidade.descr')
                           ->join('especialidade', 'especialidade.id', 'especialidade_pessoa.id_especialidade')
                        //    ->where('id_emp', getEmpresa())
                           ->where('especialidade_pessoa.id_profissional', Auth::user()->id_profissional)
                           ->groupBy('especialidade.id', 'especialidade.descr')
                           ->get();
        $empresas = DB::table('empresa')
                    ->get();
        
        $contas_bancarias = DB::table('contas_bancarias')
                            ->select('contas_bancarias.id', 'contas_bancarias.titular')
                            ->where('contas_bancarias.id_emp', getEmpresa())
                            ->get();

        if ($primeira_consulta == null) $primeira_consulta = 'Sem atendimentos registrados.';
        else                            $primeira_consulta = date('d/m/Y', strtotime($primeira_consulta));

        $consulta = DB::select(DB::raw("
            SELECT SUM(pedido_planos.qtd_total) AS total

            FROM pedido_planos
            
            JOIN pedido
                ON pedido.id = pedido_planos.id_pedido
                
            WHERE pedido.id_paciente = ".$id_pessoa."
              AND pedido.lixeira = 0
              AND pedido.data_validade >= CURDATE()
              AND pedido.status <> 'C'
        "));

        $total = intval($consulta[0]->total);

        $consulta = DB::select(DB::raw("
            SELECT COUNT(agenda.id) AS subtrair

            FROM agenda

            JOIN pedido
                ON pedido.id = agenda.id_pedido

            WHERE pedido.id_paciente = ".$id_pessoa."
              AND pedido.lixeira = 0
              AND pedido.data_validade >= CURDATE()
              AND agenda.lixeira = 0
              AND agenda.status <> 'C'
              AND pedido.status <> 'C'
        "));

        $disponivel = $total - intval($consulta[0]->subtrair);

        $consulta = DB::select(DB::raw("
            SELECT COUNT(agenda.id) AS agendados

            FROM agenda

            JOIN pedido
                ON pedido.id = agenda.id_pedido

            WHERE pedido.id_paciente = ".$id_pessoa."
              AND pedido.lixeira = 0
              AND pedido.data_validade >= CURDATE()
              AND agenda.lixeira = 0
              AND agenda.status NOT IN ('C', 'F')
              AND pedido.status <> 'C'
        "));

        $agendados = intval($consulta[0]->agendados);

        return view(
            'prontuario',
            compact(
                'pessoa',
                'primeira_consulta',
                'tabela_precos',
                'especialidades',
                'idade',
                'especialidades_',
                'empresas',
                'contas_bancarias',
                'total',
                'disponivel',
                'agendados'
            )
        );
    }

    public function listarProfissionais($filtro) {
        $pessoas = DB::select(DB::raw("
            SELECT
                pessoa.id,
                pessoa.cod_interno,
                pessoa.nome_fantasia,
                pessoa.celular1,
                pessoa.email,
                pessoa.colaborador,
                pessoa.administrador,
                DATE_FORMAT(hist.data,'%d/%m/%Y') AS data,
                hist.acao
            
            FROM pessoa
            
            LEFT JOIN empresas_profissional
                ON empresas_profissional.id_profissional = pessoa.id
            
            JOIN (
                SELECT * 
                FROM historico 
                WHERE id IN (
                    SELECT id 
                    FROM (
                        SELECT 
                            id_pessoa, 
                            MAX(id) AS id 
                        FROM historico 
                        GROUP BY id_pessoa
                    ) AS tab
                )
            ) AS hist ON hist.id_pessoa = pessoa.id
            
            WHERE (hist.acao = '".$filtro."' OR '".$filtro."' = 'T')
              AND pessoa.lixeira = 0
            
            GROUP BY
                pessoa.id,
                pessoa.cod_interno,
                pessoa.nome_fantasia,
                pessoa.celular1,
                pessoa.email,
                pessoa.colaborador,
                pessoa.administrador,
                hist.data,
                hist.acao
            
            ORDER BY pessoa.nome_fantasia 
        "));

        $especialidades = DB::table('especialidade')
                        ->where('lixeira', false)
                        ->get();

        $convenios = DB::table('convenio')
                    // ->where('id_emp', getEmpresa())
                    ->get();

        $etiquetas = DB::table('etiqueta')
                    // ->where('id_emp', getEmpresa())
                    ->get();
        $user = Pessoa::find(Auth::user()->id_profissional);
        $regra_associado = DB::table('associados_regra')
                    ->where('ativo', true)
                    ->value('dias_pos_fim_contrato');
        $associados = array();
        $empresas = DB::table('empresa')
                    ->get();
        foreach($pessoas as $pessoa){
            $associado = DB::table('pedido')
                            ->where('id_paciente', $pessoa->id)
                            ->where(function($sql) {
                                $sql->where('pedido.lixeira', false)
                                    ->orWhere('pedido.lixeira', null);
                            })
                            ->orderBy('data_validade', 'DESC')
                            ->first();
            $associado_old = DB::table('old_contratos')
                            ->where('pessoas_id', $pessoa->id)
                            ->orderBy('old_contratos.datafinal', 'DESC')
                            ->first();

            if ($associado && !$associado_old){
                if (date($associado->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')) {
                    array_push($associados, 1);
                }
                else array_push($associados, 0);
            }
            else if (!$associado && $associado_old){
                if (date($associado_old->datafinal, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')) {
                    array_push($associados, 1);
                }
                else array_push($associados, 0);
            }else if ($associado && $associado_old){
                if (date($associado->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d') || 
                    date($associado_old->datafinal, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')){
                    array_push($associados, 1);
                }else array_push($associados, 0);
            }
            else array_push($associados, 0);
        }

        return view('pessoa', compact('pessoas', 'especialidades', 'convenios', 'etiquetas', 'associados', 'user', 'empresas', 'filtro'));
    }
    public function retornar_usuario () {
        return Pessoa::find(Auth::user()->id_profissional);
    }
    public function listarClientes() {
        $pessoas = DB::table('pessoa')
                ->select(
                    'pessoa.id',
                    'pessoa.cod_interno',
                    'pessoa.nome_fantasia',
                    'pessoa.celular1',
                    'pessoa.email',
                    'pessoa.colaborador'
                )
                // ->where('pessoa.id_emp', getEmpresa())
                ->where('pessoa.cliente', '<>', 'N')
                ->where('pessoa.lixeira', false)
                ->orderby('pessoa.nome_fantasia')
                ->get();

        $convenios = DB::table('convenio')
                    // ->where('id_emp', getEmpresa())
                    ->get();

        return view('pessoa', compact('pessoas', 'convenios'));
    }

    public function listar_empresas() {
        return json_encode(
            DB::table('empresa')
            ->get()
        );
    }

    public function psqPaciente(Request $request) {
        $retorno = $this->psqPacienteAux("", $request);
        if (sizeof($retorno->pessoas) < 1) $retorno = $this->psqPacienteAux("%", $request);
        $retorno->filtro = $request->filtro;
        $retorno->admin = Pessoa::find(Auth::user()->id_profissional)->administrador == 'S';
        return json_encode($retorno);
    }

    private function psqPacienteAux($inicio, Request $request) {
        $sql = array("(", "(");
        $fltr = explode(" ", $request->filtro);
        for ($i = 0; $i < count($sql); $i++) {
            for ($j = 0; $j < count($fltr); $j++) {
                if ($sql[$i] != "(") $sql[$i] .= " and ";
                $sql[$i] .= "pessoa.";
                $sql[$i] .= $i == 0 ? "nome_fantasia" : "nome_reduzido";
                $sql[$i] .= " like '".$inicio.$fltr[$j]."%'";
            }
            $sql[$i] .= ")";
        }
        $data = new \StdClass;
        $data->pessoas = DB::select(DB::raw($this->queryPaciente(0, 
            $sql[0]." OR ".$sql[1]." OR '".$request->filtro."' = '' OR 
            pessoa.email LIKE '%". $request->filtro ."%' OR
            pessoa.celular1 LIKE '%". $request->filtro ."%' OR
            REPLACE(REPLACE(pessoa.cpf_cnpj, '.', ''),'-', '') LIKE '%". $request->filtro ."%'"
        )));
        return $data;
    }

    public function listarPacientes() {
        $pessoas = DB::select(DB::raw($this->queryPaciente(0, "")));

        $convenios = DB::table('convenio')
                    // ->where('id_emp', getEmpresa())
                    ->get();
        //return json_encode($pessoas);

        return view('pessoa', compact('pessoas', 'convenios'));
    }

    public function listar(Request $request) {
        $retorno = $this->listarAux("", $request);
        if (sizeof($retorno->pessoas) < 1) $retorno = $this->listarAux("%", $request);
        $retorno->filtro = $request->filtro;
        $retorno->admin = Pessoa::find(Auth::user()->id_profissional)->administrador == 'S';
        return json_encode($retorno);
    }
    private function listarAux($inicio, Request $request) {
        try {
            $sql = array("(", "(");
            $fltr = explode(" ", $request->filtro);
            for ($i = 0; $i < count($sql); $i++) {
                for ($j = 0; $j < count($fltr); $j++) {
                    if ($sql[$i] != "(") $sql[$i] .= " and ";
                    $sql[$i] .= "pessoa.";
                    $sql[$i] .= $i == 0 ? "nome_fantasia" : "nome_reduzido";
                    $sql[$i] .= " like '".$inicio.$fltr[$j]."%'";
                }
                $sql[$i] .= ")";
            }
            $data = new \StdClass;
            $data->mod_cod_interno = getEmpresaObj()->mod_cod_interno;

            if ($request->apenas_pre_cadastro == 'true') {
                $data->pessoas = DB::table('pessoa')
                                ->select(
                                    'id',
                                    'id_emp',
                                    'cod_interno',
                                    'nome_fantasia',
                                    'celular1',
                                    'paciente',
                                    'colaborador',
                                    DB::raw(
                                        '(SELECT especialidade.descr' .
                                        '   FROM especialidade_pessoa' .
                                        '   LEFT OUTER JOIN especialidade ON especialidade.id = especialidade_pessoa.id_especialidade' .
                                        '  WHERE especialidade_pessoa.id_profissional = pessoa.id' .
                                        '  ORDER BY especialidade.id' .
                                        '  LIMIT 1) AS descr_especialidade'
                                    ),
                                    DB::raw("CASE WHEN email  IS NULL THEN '' ELSE email  END AS email"),
                                    DB::raw("CASE WHEN cidade IS NULL THEN '' ELSE cidade END AS cidade"),
                                    DB::raw("CASE WHEN uf     IS NULL THEN '' ELSE uf     END AS uf")
                                )
                                // ->where('id_emp', getEmpresa())
                                ->where('cliente', 'N')
                                ->where($request->tipo_pessoa, $request->tipo)
                                ->whereRaw("(
                                    ".$sql[0]." OR ".$sql[1]." OR '".$request->filtro."' = '' OR 
                                    email LIKE '%". $request->filtro ."%' OR
                                    celular1 like '%". $request->filtro ."%' OR
                                    REPLACE(REPLACE(cpf_cnpj, '.', ''),'-', '') like '%". $request->filtro ."%'
                                )")
            
                            
                                ->whereRaw(
                                    '(SELECT COUNT(*)' .
                                    '   FROM convenio_pessoa' .
                                    '  WHERE convenio_pessoa.id_paciente = pessoa.id) <= 1'
                                )
                                ->where('lixeira', false)
                                ->orderby('nome_fantasia')
                                ->take(40)
                                ->get();

            } else {
                $data->pessoas = DB::table('pessoa')
                                ->select(
                                    'id',
                                    'id_emp',
                                    'cod_interno',
                                    'nome_fantasia',
                                    'celular1',
                                    'paciente',
                                    'colaborador',
                                    DB::raw(
                                        '(SELECT especialidade.descr' .
                                        '   FROM especialidade_pessoa' .
                                        '   LEFT OUTER JOIN especialidade ON especialidade.id = especialidade_pessoa.id_especialidade' .
                                        '  WHERE especialidade_pessoa.id_profissional = pessoa.id' .
                                        '  ORDER BY especialidade.id' .
                                        '  LIMIT 1) AS descr_especialidade'
                                    ),
                                    DB::raw("CASE WHEN email  IS NULL THEN '' ELSE email  END AS email"),
                                    DB::raw("CASE WHEN cidade IS NULL THEN '' ELSE cidade END AS cidade"),
                                    DB::raw("CASE WHEN uf     IS NULL THEN '' ELSE uf     END AS uf")
                                )
                                // ->where('id_emp', getEmpresa())
                                // ->where('cliente', 'N')
                                // ->where($request->tipo_pessoa, $request->tipo)
                                ->whereRaw("(
                                    ".$sql[0]." OR ".$sql[1]." OR '".$request->filtro."' = '' OR 
                                    email LIKE '%". $request->filtro ."%' OR
                                    celular1 like '%". $request->filtro ."%' OR
                                    REPLACE(REPLACE(cpf_cnpj, '.', ''),'-', '') like '%". $request->filtro ."%'
                                )")
                                ->where('lixeira', false)
                                ->orderby('nome_fantasia')
                                ->take(40)
                                ->get();
            }
            $regra_associado = DB::table('associados_regra')
                           ->where('ativo', true)
                           ->value('dias_pos_fim_contrato');

            $associados = array();
            foreach($data->pessoas as $pessoa){
                $associado = DB::select(
                    DB::raw("
                        select * from pedido
                                      left join pedido_planos on pedido_planos.id_pedido = pedido.id
                                      left join tabela_precos on tabela_precos.id = pedido_planos.id_plano
                        where tabela_precos.associado = 'S' AND
                              pedido.id_paciente = ". $pessoa->id ." AND
                              pedido.lixeira = 0 AND 
                              pedido.data_validade >= '". date('Y-m-d') ."'
                        order by pedido.data_validade DESC
                    ")
                );
                $associado_old = DB::table('old_contratos')
                                ->where('pessoas_id', $pessoa->id)
                                ->orderBy('old_contratos.datafinal', 'DESC')
                                ->first();

                if (sizeof($associado) > 0 && !$associado_old){
                    if (date($associado[0]->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')) {
                        array_push($associados, 1);
                    }
                    else array_push($associados, 0);
                }
                else if (!sizeof($associado) == 0 && $associado_old){
                    if (date($associado_old->datafinal, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')) {
                        array_push($associados, 1);
                    }
                    else array_push($associados, 0);
                }else if (sizeof($associado) > 0 && $associado_old){
                    if (date($associado[0]->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d') || 
                        date($associado_old->datafinal, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')){
                        array_push($associados, 1);
                    }else array_push($associados, 0);
                }
                else array_push($associados, 0);
            }
            $data->asc = $associados;

            return $data;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function verificar_associado($id_pessoa){
        $regra_associado = DB::table('associados_regra')
                           ->where('ativo', true)
                           ->value('dias_pos_fim_contrato');

        $associado = DB::table('pedido')
                    ->where('id_paciente', $id_pessoa)
                    ->where(function($sql) {
                        $sql->where('pedido.lixeira', false)
                            ->orWhere('pedido.lixeira', null);
                    })
                    ->orderBy('data_validade', 'DESC')
                    ->first();
        if (!$associado){
            return 'false';
        }else{
            if (date($associado->data_validade, strtotime('+'.$regra_associado . 'days')) > date('Y-m-d')){
                return 'true';
            }else return 'false';
        }
    }
    public function listar_membros() {
        return DB::table('pessoa')
               ->where(function($sql) {
                    $sql->where('colaborador', 'P')
                        ->orWhere('colaborador', 'A');
               })
               ->where('lixeira', '<>', 1)
               ->where('id', '>', 1)
               ->orderBy('pessoa.nome_fantasia')
               ->get();
    }
    public function listar_membros_e_horarios(Request $request) {
        $query = "
            SELECT
                pessoa.id,
                pessoa.nome_fantasia,
                
                grade_horario.id as id_horario,
                grade_horario.dia_semana,
                grade_horario.hora,
                
                empresas_profissional.id_emp,

                empresa.descr
            
            FROM
                grade
                
            LEFT JOIN grade_horario
                ON grade_horario.id_grade = grade.id
            
            LEFT JOIN pessoa
                ON pessoa.id = grade.id_profissional
                
            LEFT JOIN empresas_profissional
                ON empresas_profissional.id_profissional = pessoa.id
            
            INNER JOIN empresa
                ON empresa.id = empresas_profissional.id_emp

            LEFT JOIN agenda
                ON agenda.dia_semana = grade_horario.dia_semana
                AND
                agenda.id_profissional = pessoa.id
                AND
                agenda.hora = grade_horario.hora
                AND
                agenda.data >= '".$request->inicio."'
                AND
                agenda.data <= '".$request->final."'
            
            WHERE
                (pessoa.colaborador = 'P' or pessoa.colaborador = 'A')
                AND
                pessoa.lixeira <> 1
                AND
                pessoa.id = ".$request->idProf."
                
                AND

                grade.ativo = 1
                
                AND

                grade_horario.dia_semana <> 'NULL'

            GROUP BY 
                pessoa.id,
                pessoa.nome_fantasia,

                grade_horario.id,
                grade_horario.dia_semana,
                grade_horario.hora,
                
                empresas_profissional.id_emp,

                empresa.descr


            ORDER BY
                empresa.descr,
                pessoa.nome_fantasia
        ";
        //return $query;
        return DB::select(DB::raw($query));
    }
    public function resumoPorPessoa($id_pessoa) {
        try {
            $evolucao = DB::table('evolucao')
                    ->select(
                        DB::raw("'evolucao' AS tabela"),
                        DB::raw("evolucao_tipo.descr AS titulo"),
                        'evolucao.id',
                        'evolucao.data',
                        'evolucao.hora',
                        DB::raw("pessoa.nome_fantasia AS responsavel"),
                        DB::raw(
                            "CASE " .
                            "    WHEN evolucao.cid IS NULL OR evolucao.estado IS NULL THEN CONCAT('<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "    ELSE CONCAT('<b>CID:</b>', evolucao.cid, '.', '<b>Estado:</b>', evolucao.estado, '.', '<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "END AS descricao"
                        ),
                        'evolucao_tipo.prioritario'
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'evolucao.id_profissional')
                    ->leftjoin('evolucao_tipo', 'evolucao_tipo.id', 'evolucao.id_evolucao_tipo')
                    ->where('evolucao.id_paciente', $id_pessoa);

            $evolucao_pedido = DB::table('pedido_servicos')
                            ->select(
                                DB::raw(
                                    " CASE" .
                                    "   WHEN pedido_servicos.status = 'F' THEN 'evolucao_pedido_finalizada'" .
                                    "   ELSE 'evolucao_pedido'" .
                                    " END AS tabela"
                                ),
                                DB::raw(
                                    "CONCAT(" .
                                    "   procedimento.descr_resumida," .
                                    "   CASE WHEN procedimento.obs          IS NOT NULL THEN CONCAT(' (', pedido_servicos.obs, ')')               ELSE '' END," .
                                    "   CASE WHEN procedimento.dente_regiao IS NOT NULL THEN CONCAT(' - Dente Região: ', procedimento.dente_regiao) ELSE '' END," .
                                    "   CASE WHEN procedimento.face         IS NOT NULL THEN CONCAT(' - Face: ', procedimento.face)                 ELSE '' END" .
                                    ") AS titulo"
                                ),
                                'pedido_servicos.id',
                                DB::raw(
                                    "(SELECT ep.data" .
                                    "   FROM evolucao_pedido ep" .
                                    "  WHERE ep.id_pedido_servicos = pedido_servicos.id" .
                                    "  ORDER BY ep.data DESC" .
                                    "  LIMIT 1) AS data"
                                ),
                                DB::raw(
                                    "(SELECT ep.hora" .
                                    "   FROM evolucao_pedido ep" .
                                    "  WHERE ep.id_pedido_servicos = pedido_servicos.id" .
                                    "  ORDER BY ep.data DESC," .
                                    "           ep.hora DESC" .
                                    "  LIMIT 1) AS hora"
                                ),
                                DB::raw(
                                    " CASE " .
                                    "   WHEN pedido_servicos.status = 'F' THEN CONCAT(SUBSTRING_INDEX(prof_finalizado.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(prof_finalizado.nome_fantasia, ' ', -1)) " .
                                    "   ELSE CONCAT(SUBSTRING_INDEX(profissional.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(profissional.nome_fantasia, ' ', -1)) " .
                                    " END AS responsavel"
                                ),
                                DB::raw(
                                    "GROUP_CONCAT(CONCAT(" .
                                    "    '<b>'," .
                                    "    CASE WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido" .
                                    "         ELSE CONCAT(SUBSTRING_INDEX(profissional.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(profissional.nome_fantasia, ' ', -1)) END," .
                                    "    ' no dia '," .
                                    "    DATE_FORMAT(evolucao_pedido.data,'%d/%m/%Y')," .
                                    "    ' às '," .
                                    "    SUBSTRING(evolucao_pedido.hora, 1, 5)," .
                                    "    ' - '," .
                                    "    '</b>'," .
                                    "    evolucao_pedido.diagnostico, '.'" .
                                    ") SEPARATOR '<br>') AS descricao"
                                ),
                                DB::raw('1 AS prioritario')
                            )
                            ->leftjoin('evolucao_pedido', 'evolucao_pedido.id_pedido_servicos', 'pedido_servicos.id')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'evolucao_pedido.id_profissional')
                            ->leftJoin('pessoa AS prof_finalizado', 'prof_finalizado.id', 'pedido_servicos.id_prof_finalizado')
                            ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
                            ->where('pedido_servicos.status', '<>', 'C')
                            ->where('evolucao_pedido.id_paciente', $id_pessoa)
                            ->groupby('procedimento.descr_resumida')
                            ->groupby('pedido_servicos.id')
                            ->groupby('profissional.nome_fantasia');

            $agenda = DB::table('agenda')
                    ->select(
                        DB::raw("'agenda' AS tabela"),
                        DB::raw("CONCAT(tipo_procedimento.descr, ' | ', IFNULL(pessoa.nome_reduzido, pessoa.nome_fantasia))  AS titulo"),
                        'agenda.id',
                        'agenda.updated_at AS data',
                        DB::raw('TIME(agenda.updated_at) AS hora'),
                        DB::raw("CONCAT(SUBSTRING_INDEX(agenda.created_by, ' ', 1), ' ', SUBSTRING_INDEX(agenda.created_by, ' ', -1)) AS responsavel"),
                        DB::raw(
                            "CONCAT(" .
                            "    'Marcado para o dia ', " .
                            "    DATE_FORMAT(agenda.data, '%d/%m/%Y')," .
                            "    ' às ', " .
                            "    SUBSTRING(agenda.hora, 1, 5), " .
                            "    CASE" .
                            "        WHEN agenda.status = 'C' THEN" .
                            "            CASE " .
                            "                WHEN agenda.motivo_cancelamento = 1 THEN '.<br>Consulta cancelada por solicitação do cliente.' " .
                            "                WHEN agenda.motivo_cancelamento = 2 THEN '.<br>Consulta cancelada por solicitação do profissional.' " .
                            "                WHEN agenda.motivo_cancelamento = 3 THEN '.<br>Consulta cancelada por solicitação da clínica.' " .
                            "                WHEN agenda.motivo_cancelamento = 4 THEN agenda.obs_cancelamento " .
                            "                ELSE '<br>Consulta Cancelada.' " .
                            "            END" .
                            "        ELSE ''" .
                            "    END," .
                            "    '<br><b>Observações: </b>', " .
                            "    IFNULL(agenda.obs, '.')" .
                            ") AS descricao"),
                        DB::raw('0 AS prioritario')
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_profissional')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                    ->where('agenda.id_paciente', $id_pessoa);

            $prescricao = DB::table('prescricao')
                    ->select(
                        DB::raw("'prescricao' AS tabela"),
                        DB::raw("'Prescrição' AS titulo"),
                        'prescricao.id',
                        'prescricao.updated_at AS data',
                        DB::raw('TIME(prescricao.updated_at) AS hora'),
                        DB::raw("CONCAT(SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', -1)) AS responsavel"),
                        DB::raw(
                            "CONCAT('Feito para o dia ', DATE_FORMAT(prescricao.data, '%d/%m/%Y'), '.<br><b>Descrição:</b> ', prescricao.corpo) AS descricao"
                        ),
                        DB::raw('0 AS prioritario')
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'prescricao.id_profissional')
                    ->where('prescricao.id_paciente', $id_pessoa);

            $anexos = DB::table('anexos')
                    ->select(
                        DB::raw("'anexos' AS tabela"),
                        DB::raw("'Anexo' AS titulo"),
                        'anexos.id',
                        'anexos.updated_at AS data',
                        DB::raw('TIME(anexos.updated_at) AS hora'),
                        DB::raw("'' AS responsavel"),
                        DB::raw( "CONCAT('<b>Arquivo:</b> ', anexos.titulo,'<br><b>Observação:</b> ', anexos.obs) AS descricao"),
                        DB::raw('0 AS prioritario')
                    )
                    ->where('anexos.id_paciente', $id_pessoa);

            $pedido = DB::table('pedido')
                ->select(
                    DB::raw("'pedido' AS tabela"),
                    DB::raw("'Contrato' AS titulo"),
                    'pedido.id',
                    'pedido.data',
                    'pedido.hora',
                    DB::raw("CONCAT(SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', -1)) AS responsavel"),
                    DB::raw(
                        "CONCAT('<b>Foi contratado um plano de tratamento via convênio ', convenio.descr, ' dos procedimentos:</b><br><ul>', GROUP_CONCAT('<li>', procedimento.descr, '</li>' SEPARATOR ''), '</ul>') AS descricao"
                    ),
                    DB::raw('0 AS prioritario')
                )
                ->leftjoin('pessoa', 'pessoa.id', 'pedido.id_prof_exa')
                ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                ->leftjoin('pedido_servicos', 'pedido_servicos.id_pedido', 'pedido.id')
                ->leftjoin('procedimento', 'procedimento.id', 'pedido_servicos.id_procedimento')
                ->where('pedido.id_paciente', $id_pessoa)
                ->where('pedido.status', 'F')
                ->where('pedido.lixeira', 0)
                ->groupby(
                    'pedido.id',
                    'pedido.data',
                    'pedido.hora',
                    'pessoa.nome_fantasia',
                    'convenio.descr'
                );

            $resumo = $evolucao
                ->unionAll($evolucao_pedido)
                ->unionAll($agenda)
                ->unionAll($prescricao)
                ->unionAll($anexos)
                ->unionAll($pedido)
                ->orderby('prioritario', 'DESC')
                ->orderby('data', 'DESC')
                ->orderby('hora', 'DESC')
                ->get();

            return json_encode($resumo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function ver_usuario(){
        return Pessoa::find(Auth::user()->id_profissional);
    }

    public function resumoPorParteCorpo($id_pessoa, $id_corpo) {
        try {
            $evolucao = DB::table('evolucao')
                    ->select(
                        DB::raw("'evolucao' AS tabela"),
                        DB::raw("evolucao_tipo.descr AS titulo"),
                        'evolucao.id',
                        'evolucao.data',
                        'evolucao.hora',
                        DB::raw("CONCAT(SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', -1)) AS responsavel"),
                        DB::raw(
                            "CASE " .
                            "    WHEN evolucao.cid IS NULL OR evolucao.estado IS NULL THEN CONCAT('<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "    ELSE CONCAT('<b>CID:</b>', evolucao.cid, '.', '<b>Estado:</b>', evolucao.estado, '.', '<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "END AS descricao"
                        ),
                        'evolucao_tipo.prioritario'
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'evolucao.id_profissional')
                    ->leftjoin('evolucao_tipo', 'evolucao_tipo.id', 'evolucao.id_evolucao_tipo')
                    ->where('evolucao.id_paciente', $id_pessoa)
                    ->where('evolucao.id_corpo', $id_corpo);

            $resumo = $evolucao
                ->get();

            return json_encode($resumo);
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function verificar_pre_cadastro($id_paciente) {
        try {
            return DB::table('pessoa')
                    ->select(
                        'id',
                        'id_emp',
                        'cod_interno',
                        'nome_fantasia',
                        'celular1',
                        'paciente',
                        'colaborador',
                        DB::raw(
                            '(SELECT especialidade.descr' .
                            '   FROM especialidade_pessoa' .
                            '   LEFT OUTER JOIN especialidade ON especialidade.id = especialidade_pessoa.id_especialidade' .
                            '  WHERE especialidade_pessoa.id_profissional = pessoa.id' .
                            '  ORDER BY especialidade.id' .
                            '  LIMIT 1) AS descr_especialidade'
                        ),
                        DB::raw("CASE WHEN email  IS NULL THEN '' ELSE email  END AS email"),
                        DB::raw("CASE WHEN cidade IS NULL THEN '' ELSE cidade END AS cidade"),
                        DB::raw("CASE WHEN uf     IS NULL THEN '' ELSE uf     END AS uf")
                    )
                    // ->where('id_emp', getEmpresa())
                    ->where('cliente', 'N')
                    ->where('id', $id_paciente)
                    ->whereNull('cod_interno')
                    ->whereNull('nome_reduzido')
                    ->whereNull('sexo')
                    ->whereNull('estado_civil')
                    ->whereNull('data_nasc')
                    ->whereNull('cpf_cnpj')
                    ->whereNull('rg_ie')
                    ->whereNull('celular2')
                    ->whereNull('telefone1')
                    ->whereNull('telefone2')
                    ->whereNull('cep')
                    ->whereNull('endereco')
                    ->whereNull('numero')
                    ->whereNull('complemento')
                    ->whereNull('bairro')
                    ->whereNull('cidade')
                    ->whereNull('uf')
                    ->whereNotNull('nome_fantasia')
                    ->whereNotNull('celular1')
                    ->whereRaw(
                        '(SELECT COUNT(*)' .
                        '   FROM convenio_pessoa' .
                        '  WHERE convenio_pessoa.id_paciente = pessoa.id) <= 1'
                    )
                    ->where('lixeira', false)
                    ->orderby('nome_fantasia')
                    ->count();

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    

    public function listar_corpo_json() {
        try {
            return json_encode(
                DB::table('parte_corpo')
                ->select(
                    'id',
                    'descr',
                    'obj'
                )
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    

    public function status_evolucao($id_pessoa, $id_corpo) {
        try {
            return json_encode(
                DB::table('parte_corpo')
                ->select(
                    'parte_corpo.obj',
                    DB::raw('COUNT(evolucao.id) AS qtd')
                )
                ->join('evolucao', 'evolucao.id_corpo', 'parte_corpo.id')
                ->where('parte_corpo.id', $id_corpo)
                ->where('evolucao.id_paciente', $id_pessoa)
                ->where('evolucao.lixeira', 0)
                ->groupby('parte_corpo.obj')
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function resumo_vitruviano($id_pessoa, $id_corpo) {
        try {
            return json_encode(
                DB::table('evolucao')
                    ->select(
                        DB::raw("evolucao_tipo.descr AS descr"),
                        'evolucao.id',
                        'evolucao.data',
                        'evolucao.hora',
                        DB::raw("CONCAT(SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', 1), ' ', SUBSTRING_INDEX(pessoa.nome_fantasia, ' ', -1)) AS responsavel"),
                        DB::raw(
                            "CASE " .
                            "    WHEN evolucao.cid IS NULL OR evolucao.estado IS NULL THEN CONCAT('<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "    ELSE CONCAT('<b>CID:</b>', evolucao.cid, '.', '<b>Estado:</b>', evolucao.estado, '.', '<b>Diagnóstico:</b>', evolucao.diagnostico) " .
                            "END AS descricao"
                        ),
                        'evolucao_tipo.prioritario'
                    )
                    ->leftjoin('pessoa', 'pessoa.id', 'evolucao.id_profissional')
                    ->leftjoin('evolucao_tipo', 'evolucao_tipo.id', 'evolucao.id_evolucao_tipo')
                    ->leftjoin('parte_corpo', 'parte_corpo.id', 'evolucao.id_corpo')
                    ->where('evolucao.id_paciente', $id_pessoa)
                    ->where('evolucao.id_corpo', $id_corpo)
                    ->where('evolucao.lixeira', 0)
                    ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function salvar_regra_associados(Request $request){
        if ($request->id){
            $regra = AssociadosRegra::find($request->id);
        }
        else {
            $regra = new AssociadosRegra;
        }
        $ativos = DB::table('associados_regra')
                  ->where('ativo', true)
                  ->get();
        foreach($ativos as $ativo){
            $aux = AssociadosRegra::find($ativo->id);
            $aux->ativo = false;
            $aux->save();
        }

        $regra->id_emp = getEmpresa();
        $regra->dias_pos_fim_contrato = $request->dias;
        $regra->ativo = true;
        $regra->lixeira = false;
        $regra->save();
        

        return redirect('/regras');
    }

    public function excluir_regras_associados($id) {
        $regra = AssociadosRegra::find($id);
        $regra->lixeira = true;
        $regra->save();
        return $regra->dias_pos_fim_contrato;
    }
    public function exibir_regras_associados($id) {
        $regras = AssociadosRegra::find($id);
        return $regras;
    }
    public function exibir_regras() {
        $regras = DB::table("associados_regra")
                //   ->where('id_emp', getEmpresa())
                  ->where('lixeira', '<>', true)
                  ->get();

        return view('regras_associados', compact('regras'));
    }
    public function alterar_empresa(Request $request) {
        $user = Users::find(Auth::user()->id);
        $user->id_emp = $request->id_emp;
        $user->save();
        
        return $user->id_emp;
    }
    public function empresa() {
        $data = new \StdClass;

        $data->empresa = Auth::user()->id_emp;
        $data->empresas = DB::table('empresa')
                          ->select('empresas_profissional.id_emp As id', 'empresa.descr As descr')
                          ->join('empresas_profissional', 'empresas_profissional.id_emp', 'empresa.id')
                          ->where('empresas_profissional.id_profissional', Auth::user()->id_profissional)
                          ->get();
        return json_encode($data);
    }


    public function agendados_atividade_modal ($id_paciente){
        $pedidos = DB::table('pedido')
            ->selectRaw('Group_Concat(pedido.id) AS ids')
            ->where('lixeira', 0)
            ->where('status', 'F')
            ->where('id_paciente', $id_paciente)
            ->groupBy('id_paciente')
            ->value('ids');
            $pedidos = "(" . $pedidos . ")";

        $agendamentos = DB::table('agenda')
                            ->select(
                                'agenda.id',
                                'agenda.id_emp',
                                'agenda.id_status',
                                'agenda.status',
                                'agenda.id_tipo_procedimento',
                                'tipo_procedimento.descr AS tipo_procedimento',
                                'agenda.id_grade_horario',
                                'agenda.hora',
                                'agenda.data',
                                'agenda.id_paciente',
                                'pessoa.nome_fantasia AS nome_paciente',
                                DB::raw(
                                    '(CASE' .
                                    '   WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido ' .
                                    '   ELSE profissional.nome_fantasia ' .
                                    'END) AS nome_profissional'
                                ),
                                'procedimento.descr AS descr_procedimento',
                                'convenio.descr AS convenio_nome',
                                'agenda_status.permite_editar',
                                'agenda_status.libera_horario',
                                'agenda_status.permite_fila_espera',
                                'agenda_status.permite_reagendar',
                                'agenda_status.descr AS descr_status',
                                'agenda_status.cor AS cor_status',
                                'agenda_status.cor_letra',
                                'grade.min_intervalo',
                                'agenda.id_confirmacao',
                                'agenda_confirmacao.descr AS descr_confirmacao',
                                DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs"),
                                DB::raw("(select 0) AS antigo")
                                )
                                ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_modalidade')
                                ->leftjoin('convenio', 'convenio.id', 'agenda.id_convenio')
                                ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                                ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                                ->leftjoin('grade_horario', 'grade_horario.id', 'agenda.id_grade_horario')
                                ->leftjoin('grade', 'grade.id', 'grade_horario.id_grade')
                                ->leftjoin('fila_espera', 'fila_espera.id_agendamento', 'agenda.id')
                                ->leftjoin('agenda_status', 'agenda_status.id', 'agenda.id_status')
                                ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                                ->whereRaw('(agenda.id_pedido in'. $pedidos ."AND agenda.lixeira = 0 AND agenda.status = 'A')")
                                ->where('agenda.id_emp', getEmpresa())
                                ->where('agenda.lixeira', 0)
                
                                ->get();

        return json_encode($agendamentos);
    }



    public function atualizar_cadastro_contrato(Request $request) {
        $pessoa = Pessoa::find($request->id_paciente);

        $pessoa->nome_fantasia = $request->nome_fantasia;
        $pessoa->cpf_cnpj      = $request->cpf_cnpj;
        $pessoa->rg_ie         = $request->rg_ie;
        $pessoa->cep           = $request->cep;
        $pessoa->cidade        = $request->cidade;
        $pessoa->uf            = $request->uf;
        $pessoa->endereco      = $request->endereco;
        $pessoa->numero        = $request->numero;
        $pessoa->bairro        = $request->bairro;
        $pessoa->complemento   = $request->complemento;
        $pessoa->email         = $request->email;
        $pessoa->celular1      = $request->celular;

        $pessoa->save();

        return 'true';
    }

    public function verificar_duplicidade($cpf, $id_pessoa) {
        // return str_replace('-', '', str_replace('.', '', str_replace('.', '', $cpf)));
        $aux = DB::table('pessoa')
               ->whereRaw("
                lixeira = 0 AND
                (
                    cpf_cnpj = '". $cpf ."' OR
                    REPLACE(REPLACE(cpf_cnpj, '-', ''),'.','') = '".  str_replace('-', '', str_replace('.', '', str_replace('.', '', $cpf)))."'
                )
               ")
               ->count();
        // return $aux;
        if ($aux > 0 && $id_pessoa == 0){
            return 'N';
        }
        else if ($aux > 1 && $id_pessoa != 0) {
            return 'N';
        }
        else {
            return 'S';
        }
    }

    public function getNome($id_pessoa) {
        return DB::table("pessoa")
                   ->select("nome_reduzido")
                   ->where("id", $id_pessoa)
                   ->get();
    }

    private function queryPaciente($id, $filtro) {
        $query = "SELECT pessoa.*, ";
        if ($id > 0) $query .= " IFNULL(tab.ct, 0) AS qtde_consulta, ";
        $query .= "
                CASE WHEN (associado.num IS NULL) THEN 'N' ELSE 'S' END AS associado,
                CASE WHEN ((iec.ultimo < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)) OR iec.ultimo IS NULL) THEN 'S' ELSE 'N' END AS iec_atrasado
            FROM
                pessoa
                LEFT JOIN (
                    SELECT
                        iec_total.id_paciente,
                        MIN(iec_total.ultimo_tipo) AS ultimo
                    FROM (
                        SELECT
                            id_questionario,
                            id_paciente,
                            MAX(IEC_pessoa.updated_at) AS ultimo_tipo
                        FROM IEC_pessoa
                        LEFT JOIN IEC_questionario 
                            ON IEC_questionario.id = IEC_pessoa.id_questionario
                        WHERE IEC_pessoa.lixeira = 0
                            AND IEC_questionario.ativo = 'S'
                            AND IEC_questionario.lixeira = 'N'
                        GROUP BY
                            id_paciente,
                            id_questionario
                    ) AS iec_total
                    GROUP BY id_paciente
                ) AS iec ON iec.id_paciente = pessoa.id
                LEFT JOIN (
                    SELECT
                        id_paciente AS id_pessoa,
                        COUNT(*) AS num
                    FROM pedido
                    LEFT JOIN pedido_planos
                        ON pedido_planos.id_pedido = pedido.id
                    LEFT JOIN tabela_precos
                        ON tabela_precos.id = pedido_planos.id_plano
                    WHERE tabela_precos.associado = 'S'
                        AND pedido.lixeira = 0
                        AND pedido.data_validade >= CURDATE()
                    GROUP BY id_paciente
                ) AS associado ON associado.id_pessoa = pessoa.id
        ";
        if ($id > 0) {
            $query .= "
                LEFT JOIN (
                    SELECT
                        id_paciente,
                        COUNT(id_paciente) AS ct
                    FROM agenda
                    GROUP BY id_paciente
                ) AS tab ON tab.id_paciente = pessoa.id
            ";
        }
        $query .= " WHERE ";
        if ($id == 0) {
            $query .= "
                (pessoa.paciente = 'S' OR pessoa.colaborador <> 'N')
                AND pessoa.cliente = 'N'
                AND pessoa.lixeira = 0
            ";
            if ($filtro != "") $query .= " AND (".$filtro.")";
            $query .= " ORDER BY pessoa.nome_fantasia LIMIT ";
            $query .= $filtro != "" ? 40 : 100;
        } else $query .= "pessoa.id = ".$id;
        return $query;
    }

    public function atividades($id) {
        return json_encode(DB::select(DB::raw("
            SELECT
                pp.id,
                pp.id_pedido,
                CASE
                    WHEN pp.descr IS NULL THEN tp.descr
                    ELSE pp.descr
                END AS nome,
                (GREATEST(pp.qtd_total, pp.qtd_original) - IFNULL(ag.cont, 0)) AS disponivel
            
            FROM pedido
            
            JOIN pedido_planos AS pp
                ON pp.id_pedido = pedido.id

            LEFT JOIN (
                SELECT
                    id_tabela_preco,
                    id_pedido,
                    COUNT(id) AS cont

                FROM agenda

                WHERE lixeira = 0
                  AND status <> 'C'

                GROUP BY
                    id_tabela_preco,
                    id_pedido
            ) AS ag ON ag.id_tabela_preco = pp.id_plano AND ag.id_pedido = pp.id_pedido

            LEFT JOIN tabela_precos AS tp
                ON tp.id = pp.id_plano

            WHERE pedido.status <> 'C'
              AND pedido.lixeira = 0
              AND pedido.data_validade >= CURDATE()
              AND pedido.id_paciente = ".$id
        )));
    }
}