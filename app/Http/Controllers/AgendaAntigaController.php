<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\OldMovAtividades;
use App\OldAtividades;
use App\OldFinanreceber;
use App\Pedido;
use App\PedidoFormaPag;
use App\PedidoParcela;
use App\PedidoServicos;
use App\PedidoPlanos;
use App\PedidoPessoas;
use App\Agenda;
use App\Pessoa;
use App\GradeHorario;
use App\Helpers;
use App\TabelaPrecos;
use Illuminate\Http\Request;

class AgendaAntigaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function salvar_agendamento_antigo(Request $request){
        $data = new \DateTime(str_replace('/', '-', $request->data));
        if ($request->id){
            $agendamento = OldMovAtividades::find($request->id);

            if ($agendamento->data != $data->format('Y-m-d') || $agendamento->id_grade != $request->id_grade_horario) {

                $agendamento->id_status = 16;
                $agendamento->status = 'C';
                $agendamento->save();

                $novo_agendamento = new OldMovAtividades;
                $novo_agendamento->id_atividade = $request->modalidade_id;
                $novo_agendamento->seq = (DB::table('old_mov_atividades')->where('id_atividade',$request->modalidade_id)->max('seq')) + 1;
                $novo_agendamento->id_membro = $request->id_profissional;
                $novo_agendamento->id_grade =  $request->id_grade_horario;
                $novo_agendamento->data = $data->format('Y-m-d');
                $novo_agendamento->hora = $request->hora;
                $novo_agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
                $novo_agendamento->usu_criado = Auth::user()->name;
                $novo_agendamento->dt_criado = date('Y-m-d');
                $novo_agendamento->hr_criado = date('H:i:s');
                $novo_agendamento->status = 'A';
                $novo_agendamento->id_status = statusCasoReagendar()->id;
                $novo_agendamento->id_tipo_procedimento = 1;
                $novo_agendamento->save();

                return 'true';
            }
            else {
                if ($agendamento->id_atividade != $request->modalidade_id) {
                    $atividades = OldAtividades::find($agendamento->id_atividade);
                    $atividades->qtd = ($atividades->qtd + 1);
                    $atividades->save();
                }
                $agendamento->id_atividade = $request->modalidade_id;
                $agendamento->seq = (DB::table('old_mov_atividades')->where('id_atividade',$request->modalidade_id)->max('seq')) + 1;
                $agendamento->id_membro = $request->id_profissional;
                $agendamento->id_grade =  $request->id_grade_horario;
                $agendamento->data = $data->format('Y-m-d');
                $agendamento->hora = $request->hora;
                $agendamento->usu_criado = Auth::user()->name;
                $agendamento->dt_criado = date('Y-m-d');
                $agendamento->hr_criado = date('H:i:s');
                $agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
                $agendamento->status = 'A';
                $agendamento->id_status = $request->id_agenda_status;
                $agendamento->id_tipo_procedimento = 1;
                $agendamento->save();
                
                $atividades = OldAtividades::find($request->modalidade_id);
                $atividades->qtd = ($atividades->qtd - 1);
                $atividades->save();
                return 'true';
            }
        }
        else {
            $agendamento = new OldMovAtividades; 
            $agendamento->id_atividade = $request->modalidade_id;
            $agendamento->seq = (DB::table('old_mov_atividades')->where('id_atividade',$request->modalidade_id)->max('seq')) + 1;
            $agendamento->id_membro = $request->id_profissional;
            $agendamento->id_grade =  $request->id_grade_horario;
            $agendamento->data = $data->format('Y-m-d');
            $agendamento->hora = $request->hora;
            $agendamento->usu_criado = Auth::user()->name;
            $agendamento->dt_criado = date('Y-m-d');
            $agendamento->hr_criado = date('H:i:s');
            $agendamento->dia_semana = GradeHorario::find($request->id_grade_horario)->dia_semana;
            $agendamento->status = 'A';
            $agendamento->id_status = $request->id_agenda_status;
            $agendamento->id_tipo_procedimento = 1;
            $agendamento->save();

            $atividades = OldAtividades::find($request->modalidade_id);
            $atividades->qtd = ($atividades->qtd - 1);
            $atividades->save();
            return 'true';
        }
        
        
        return 'true';
    }
    public function deletar_agendamento(Request $request) {
        $agendamento = OldMovAtividades::find($request->id);
        $agendamento->lixeira = 1;
        $agendamento->save();
        $atividades = OldAtividades::find($agendamento->id_atividade);
        $atividades->qtd = ($atividades->qtd + 1);
        $atividades->save();
    }
    public function mudar_status(Request $request) {
        try {
            $agendamento = OldMovAtividades::find($request->id_agendamento);
            $agendamento->id_status = $request->status;
            $agendamento->status = 'A';
            $agendamento->save();
            return 'true';
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function agendamento_info($id){
        try {
            return json_encode(
                DB::table('old_mov_atividades')
                ->select(
                    'old_mov_atividades.id AS id',
                    'old_contratos.pessoas_id AS id_paciente',
                    'old_atividades.id_modalidade AS id_procedimento',
                    'old_mov_atividades.id_grade AS id_grade_horario',
                    DB::raw("CASE WHEN (old_financeira.convenio = 'S') THEN (".
                                        'old_financeira.id'.
                                ")END AS id_convenio"),
                    DB::raw("(select '') as id_confirmacao"),
                    'old_mov_atividades.id_status',
                    'old_atividades.id_contrato AS id_pedido',
                    'old_atividades.id_cardapio_preco AS id_tabela_preco',
                    'old_mov_atividades.data',
                    'grade_horario.hora',
                    'pessoa.email',
                    'pessoa.nome_fantasia AS paciente_nome',
                    'pessoa.celular1 AS celular',
                    'pessoa.telefone1 AS telefone',
                    'profissional.nome_fantasia AS profissional_nome',
                    'old_modalidades.id    AS id_modalidade',
                    'old_modalidades.descr AS descr_procedimento',
                    'old_atividades.valor_cardapio AS valor',
                    'old_mov_atividades.status AS status',
                    DB::raw("(select 'sistema antigo') AS obs"),
                    'old_mov_atividades.id_tipo_procedimento',
                    'tipo_procedimento.descr AS tipo_procedimento',
                    'tipo_procedimento.assossiar_contrato AS assossiar_contrato',
                    'tipo_procedimento.assossiar_especialidade As assosciar_especialidade',
                    DB::raw("(select '') AS tempo"),
                    DB::raw("(select 'A') AS descr_confirmacao"),
                    'old_contratos.datainicial AS data_contrato',
                    'old_contratos.id   AS id_contrato',
                    'old_modalidades.descr AS descr_plano',
                    'old_modalidades.id    AS id_plano',
                    'old_modalidades.descr        AS descricao')
                    ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                    ->leftjoin("old_modalidades", 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                    ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_financeira')
                    ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                    ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                    ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                    ->leftjoin('grade_horario', 'grade_horario.id', 'old_mov_atividades.id_grade')
                    // ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                    // ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                    // ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_procedimento')
                    // ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                    // ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                    // ->leftjoin('pedido', 'pedido.id', 'pedido.id', 'agenda.id_pedido')
                    // ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                    // ->leftjoin('tabela_precos as planos', 'planos.id', 'agenda.id_procedimento')
                    ->where('old_mov_atividades.id', $id)
                    ->first());
            } catch(\Exception $e) {
                return $e->getMessage();
        }
    }
    function editar_agendamento($id) {
        try {
            $data = new \StdClass;
            $agendamento = DB::table('old_mov_atividades')
                        ->select(
                            'old_mov_atividades.id AS id',
                            'old_contratos.pessoas_id AS id_paciente',
                            'old_atividades.id_modalidade AS id_procedimento',
                            'old_mov_atividades.id_grade AS id_grade_horario',
                            DB::raw("CASE WHEN (old_financeira.convenio = 'S') THEN (".
                                                'old_financeira.id'.
                                        ")END AS id_convenio"),
                            DB::raw("(select '') as id_confirmacao"),
                            'old_mov_atividades.id_status',
                            'old_atividades.id_contrato AS id_pedido',
                            'old_atividades.id_cardapio_preco AS id_tabela_preco',
                            'old_mov_atividades.data',
                            'grade_horario.hora',
                            'pessoa.email',
                            'pessoa.nome_fantasia AS paciente_nome',
                            'pessoa.celular1 AS celular',
                            'pessoa.telefone1 AS telefone',
                            'profissional.nome_fantasia AS profissional_nome',
                            'old_modalidades.id    AS id_modalidade',
                            'old_modalidades.descr AS descr_procedimento',
                            'old_atividades.valor_cardapio AS valor',
                            'old_mov_atividades.status AS status',
                            DB::raw("(select 'sistema antigo') AS obs"),
                            'old_mov_atividades.id_tipo_procedimento',
                            'tipo_procedimento.descr AS tipo_procedimento',
                            'tipo_procedimento.assossiar_contrato AS assossiar_contrato',
                            'tipo_procedimento.assossiar_especialidade As assosciar_especialidade',
                            DB::raw("(select '') AS tempo"),
                            DB::raw("(select 'A') AS descr_confirmacao"),
                            'old_contratos.datainicial AS data_contrato',
                            'old_contratos.id   AS id_contrato',
                            'old_modalidades.descr AS descr_plano',
                            'old_modalidades.id    AS id_plano',
                            'old_modalidades.descr        AS descricao',
                            'old_atividades.id           AS id_atividade')
                            ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                            ->leftjoin("old_modalidades", 'old_modalidades.id', 'old_atividades.id_modalidade')
                            ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                            ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                            ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_planopagamento')
                            ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                            ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                            ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                            ->leftjoin('grade_horario', 'grade_horario.id', 'old_mov_atividades.id_grade')
                            // ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
                            // ->leftjoin('pessoa AS profissional', 'profissional.id', 'agenda.id_profissional')
                            // ->leftjoin('procedimento', 'procedimento.id', 'agenda.id_procedimento')
                            // ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
                            // ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'agenda.id_confirmacao')
                            // ->leftjoin('pedido', 'pedido.id', 'pedido.id', 'agenda.id_pedido')
                            // ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                            // ->leftjoin('tabela_precos as planos', 'planos.id', 'agenda.id_procedimento')
                            ->where('old_mov_atividades.id', $id)
                            ->first();

            $atv = DB::table('old_atividades')
                    ->select('old_atividades.id AS id',
                                'old_modalidades.descr As descr',
                                'old_atividades.qtd AS atv_rest',
                                'old_atividades.qtd_ini AS total')
                    ->join('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                    ->join('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                    ->where('old_atividades.id', $agendamento->id_atividade)
                    ->first();
            $modalidades = DB::table('old_modalidades')
                           ->where('ativo', 'S')
                           ->get();
            $data->atv         = $atv;
            $data->agendamento = $agendamento;
            $data->modalidades = $modalidades;
            return json_encode($data);
            } catch(\Exception $e) {
                return $e->getMessage();
        }
    }
    public function salvar_pedido(Request $request) {
        try {
            $id_tabela_preco = DB::table('convenio')->where('id', 1)->value('id_tabela_preco');
            
            $num_pedido = DB::table('pedido')->where('id_emp', getEmpresa())->max('num_pedido');
            if ($num_pedido == null) $num_pedido = 1;
            else                     $num_pedido = $num_pedido + 1;
            if ($request->id == 0) {
                $pedido = new pedido;
                $pedido->num_pedido = $num_pedido;
            } else {
                $pedido = pedido::find($request->id);
            }
            $pedido->id_emp = getEmpresa();
            $pedido->id_paciente = $request->id_paciente;
            $pedido->id_convenio = $request->id_convenio;
            $pedido->id_prof_exa = $request->id_profissional_exa;
            $pedido->num_pedido = $num_pedido;
            $pedido->data = OldMovAtividades::find($request->id_agendamento)->data;
            $pedido->consultor = Pessoa::find($request->id_profissional_exa)->nome_fantasia;
            $pedido->hora = date('H:i:s');
            $pedido->data_validade = $request->data_validade;
            
            $pedido->status = $request->status;
            $pedido->obs = $request->obs;
            $pedido->tipo_forma_pag = $request->tipo_forma_pag;
            if ($request->id_agendamento) $pedido->tipo_contrato = 'P';
            else                          $pedido->tipo_contrato = 'N';
            $pedido->created_by = Auth::user()->id;
            if ($request->id_agendamento) $pedido->id_agendamento = $request->id_agendamento;
            $pedido->save();

            $total = 0;
            DB::table('pedido_servicos')->where('id_pedido', $pedido->id)->delete();

            $val_aux = 0;
            foreach($request->formas_pag as $forma_pag){
                $forma_pag = (object) $forma_pag;
                $val_aux += $forma_pag->forma_pag_valor;
            }
            $pedido->total = $val_aux;
            $pedido->save();

            DB::table('pedido_parcela')
            ->leftjoin('pedido_forma_pag', 'pedido_forma_pag.id', 'pedido_parcela.id_pedido_forma_pag')
            ->where('pedido_forma_pag.id_pedido', $pedido->id)
            ->delete();
            DB::table('pedido_forma_pag')->where('id_pedido', $pedido->id)->delete();
            
            foreach ($request->formas_pag as $forma_pag) {
                $forma_pag = (object) $forma_pag;

                $pedido_forma_pag = new PedidoFormaPag;
                $pedido_forma_pag->id_emp = getEmpresa();
                $pedido_forma_pag->id_pedido = $pedido->id;
                $pedido_forma_pag->id_forma_pag = $forma_pag->id_forma_pag;
                $pedido_forma_pag->id_financeira = $forma_pag->id_financeira;
                $pedido_forma_pag->num_total_parcela = $forma_pag->parcela;
                $pedido_forma_pag->valor_total = $forma_pag->forma_pag_valor;
                $pedido_forma_pag->tipo = $request->tipo_forma_pag;
                $pedido_forma_pag->save();

                $valor_parcela = $forma_pag->forma_pag_valor / $forma_pag->parcela;
                if ($valor_parcela * $forma_pag->parcela < $forma_pag->forma_pag_valor) {
                    $acrescimo = $forma_pag->forma_pag_valor - ($valor_parcela * $forma_pag->parcela);
                } else {
                    $acrescimo = 0;
                }

                for ($i = 0; $i < $forma_pag->parcela; $i++) {
                    $pedido_parcela = new PedidoParcela;
                    $pedido_parcela->id_emp = getEmpresa();
                    $pedido_parcela->id_pedido_forma_pag = $pedido_forma_pag->id;
                    $pedido_parcela->parcela = ($i + 1);
                    if ($acrescimo > 0 && $i == 0) $pedido_parcela->valor = $valor_parcela + $acrescimo;
                    else                           $pedido_parcela->valor = $valor_parcela;
                    if ($i == 0) {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento)));
                    } else {
                        $pedido_parcela->vencimento = date('Y-m-d', strtotime(date('Y-m-d', strtotime(str_replace("/", "-", $forma_pag->data_vencimento))) . ' + ' . (($i + 1) * 30) .  ' days'));
                    }
                    $pedido_parcela->save();
                }
            }
            foreach($request->planos as $plano){
                $plano = (object) $plano;

                $vigencia = TabelaPrecos::find($plano->id_plano);

                if      ($vigencia->vigencia == 30) $data_validade = date('Y-m-d', strtotime('+1 month'));
                else if ($vigencia->vigencia == 60) $data_validade = date('Y-m-d', strtotime('+2 month'));
                else if ($vigencia->vigencia == 90) $data_validade = date('Y-m-d', strtotime('+3 month'));
                else if ($vigencia->vigencia == 180) $data_validade = date('Y-m-d', strtotime('+6 month'));
                else if ($vigencia->vigencia == 360)$data_validade = date('Y-m-d', strtotime('+1 year'));
                else                 $data_validade = date('Y-m-d');

                $pedido_planos = new PedidoPlanos;
                $pedido_planos->id_emp          = getEmpresa();
                $pedido_planos->id_pedido       = $pedido->id;
                $pedido_planos->id_plano        = $plano->id_plano;
                $pedido_planos->data_validade   = $data_validade;
                $pedido_planos->qtde            = $plano->qtd;
                $pedido_planos->valor           = $plano->valor;
                $pedido_planos->save();
            }
            if ($request->id_agendamento) {
                
                $agendamento = OldMovAtividades::find($request->id_agendamento);
                $agendamento->status = 'F';
                $agendamento->id_status = 13;
                $agendamento->save();
            }
            return json_encode($pedido);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function copiar_agendamento_id(Request $request) {
        try {
            $agendamento_clip = Agenda::find($request->id_agendamento_clipboard);

            $agendamento_new = new Agenda;
            $agendamento_new->id_emp = $agendamento_clip->id_emp;
            $agendamento_new->id_grade_horario = $agendamento_clip->id_grade_horario;
            $agendamento_new->id_profissional = $agendamento_clip->id_profissional;
            $agendamento_new->id_paciente = $agendamento_clip->id_paciente;
            $agendamento_new->id_procedimento = $agendamento_clip->id_procedimento;
            $agendamento_new->id_convenio = $agendamento_clip->id_convenio;
            $agendamento_new->id_status = $agendamento_clip->id_status;
            $agendamento_new->id_confirmacao = $agendamento_clip->id_confirmacao;

            $agendamento_new->data = DB::table('agenda')->where('id', $request->id_agendamento)->value('data');
            $agendamento_new->hora = DB::table('agenda')->where('id', $request->id_agendamento)->value('hora');

            $agendamento_new->id_tipo_procedimento = $agendamento_clip->id_tipo_procedimento;
            $agendamento_new->obs = $agendamento_clip->obs;
            $agendamento_new->convenio = $agendamento_clip->convenio;
            $agendamento_new->valor = $agendamento_clip->valor;
            $agendamento_new->status = $agendamento_clip->status;
            $agendamento_new->created_by = Auth::user()->name;
            $agendamento_new->updated_by = Auth::user()->name;
            $agendamento_new->save();

            return $agendamento_new;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function dividir_horario_por_id(Request $request){
        try {
            $agenda_referencia = DB::table('agenda')
                                ->where('id', $request->id_agendamento)
                                ->first();
    
            $grade_referencia = DB::table('grade')
                                ->select('grade.*')
                                ->join('grade_horario', 'grade_horario.id_grade', 'grade.id')
                                ->where('grade_horario.id', $agenda_referencia->id_grade_horario)
                                ->first();
    
            $dia_semana = (date('w', strtotime($agenda_referencia->data)) + 1);
    
            $grade = new Grade;
            $grade->id_profissional = $agenda_referencia->id_profissional;
            $grade->id_etiqueta = $grade_referencia->id_etiqueta;
            $grade->dia_semana = $dia_semana;
            $grade->data_inicial = date('Y-m-d', strtotime($agenda_referencia->data));
            $grade->data_final = date('Y-m-d', strtotime($agenda_referencia->data));
    
            $horario_inicial = new DateTime(date('Y-m-d') . ' ' . $agenda_referencia->hora);
            $horario_inicial = $horario_inicial->add(new DateInterval('PT' . round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN) . 'M'));
            $horario_inicial = $horario_inicial->format('H:i');
            $grade->hora_inicial = $horario_inicial;
    
            $hora_final = new DateTime(date('Y-m-d') . ' ' . $agenda_referencia->hora);
            $hora_final = $hora_final->add(new DateInterval('PT' . $grade_referencia->min_intervalo . 'M'));
            $hora_final = $hora_final->format('H:i');
            $grade->hora_final = $hora_final;
    
            $grade->min_intervalo = round(($grade_referencia->min_intervalo / 2), 0, PHP_ROUND_HALF_DOWN);
    
            $grade->max_qtde_pacientes = null;
            $grade->grade_divisao = true;
            $grade->ativo = true;
            $grade->obs = 'Grade gerada ao dividir horário!';
            $grade->save();
    
            $grade_horario = new GradeHorario;
            $grade_horario->id_grade = $grade->id;
            $grade_horario->hora = $horario_inicial;
            $grade_horario->dia_semana = $dia_semana;
            $grade_horario->save();
    
            return $grade;
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function listar_modalidades_disponiveis($id){
        return DB::table('old_atividades')
                ->select('old_atividades.id AS id',
                            'old_modalidades.descr As descr',
                            'old_atividades.qtd AS atv_rest',
                            'old_atividades.qtd_ini AS total')
                ->join('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->join('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->where('old_contratos.pessoas_id', $id)
                ->where('old_atividades.qtd', '>', 0)
                // ->where('old_contratos.datafinal', '>=', date('Y-m-d'))
                ->get();
    }
    public function confirmar_agendamento(Request $request) {
        $agendamento = OldMovAtividades::find($request->id_agendamento);
        $agendamento->status = 'F';
        $agendamento->id_status = statusCasoConfirmar()->id;
        $agendamento->save();
        return 'true';
    }
    public function confirmar_agendamento_mobile(Request $request) {
        $agendamento = OldMovAtividades::find($request->id);
        $agendamento->id_confirmacao = $request->id_confirmacao;
        $agendamento->save();
        return $agendamento;
    }

    public function faturar($id_agendamento){
        return json_encode(DB::table('old_mov_atividades')
               ->select('pessoa.id AS id_pessoa',
                        'pessoa.nome_fantasia AS descr_pessoa',
                        'agenda.obs AS obs')
                        ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                        ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                        ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                        ->where('old_mov_atividades.id', $id_agendamento)
                        ->where(function($sql) {
                            $sql->where('old_mov_atividades.lixeira', false)
                                ->orWhere('old_mov_atividades.lixeira', null);
                        })
                        ->first());
    }

    function cancelar_agendamento(Request $request) {
        try {  

            $agendamento = OldMovAtividades::find($request->id);
            $agendamento->status = 'C';
            $agendamento->id_status = statusCasoCancelar()->id;
            $agendamento->usu_confirm = Auth::user()->name;
            $agendamento->updated_at = date('Y-m-d H:i:s');
            $agendamento->motivo_cancelamento = $request->motivo;
            $agendamento->obs_cancelamento = $request->observacao;
            $agendamento->save();
            
            return redirect('/agenda');
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function conferir_agendamento(Request $request){
        $data_inicial = new \DateTime($request->dinicial);
        $data_final = new \DateTime($request->dfinal);


        $atividades = DB::table('old_atividades')
                      ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                      ->where('old_contratos.pessoas_id', $request->pessoas_id)
                      ->where('old_contratos.datainicial', '>=', $data_inicial->format('Y-m-d'))
                      ->where('old_contratos.datainicial', '<=', $data_final->format('Y-m-d'))
                      ->get();
    }

    // public function listar(Request $request) {
    //     try {
    //         $historico = DB::table('historico_agenda')
    //                     ->select(
    //                         'historico_agenda.id_agenda',
    //                         'historico_agenda.id_status',
    //                         'historico_agenda.id_tipo_procedimento',
    //                         'historico_agenda.id_procedimento',
    //                         'historico_agenda.id_tipo_confirmacao',
    //                         'historico_agenda.campo',
    //                         'agenda_confirmacao.descr AS descr_tipo_confirmacao',
    //                         'tipo_procedimento.descr AS descr_tipo_procedimento',
    //                         'pessoa.nome_fantasia AS nome_paciente',
    //                         'agenda_status.descr AS descr_status',
    //                         'historico_agenda.created_at AS data',
    //                         'historico_agenda.created_by'
    //                     )
    //                     ->leftjoin('agenda', 'agenda.id', 'historico_agenda.id_agenda')
    //                     ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'historico_agenda.id_tipo_procedimento')
    //                     ->leftjoin('pessoa', 'pessoa.id', 'agenda.id_paciente')
    //                     ->leftjoin('agenda_status', 'agenda_status.id', 'historico_agenda.id_status')
    //                     ->leftjoin('agenda_confirmacao', 'agenda_confirmacao.id', 'historico_agenda.id_tipo_confirmacao')
    //                     ->where('historico_agenda.id_emp', getEmpresa())
    //                     ->where('historico_agenda.id_agenda', $request->id_agenda)
    //                     ->where(function($sql) use($request) {
    //                         if (isset($request->campo)) {
    //                             $sql->where('historico_agenda.campo', $request->campo);
    //                         }
    //                     })
    //                     ->groupby(
    //                         'historico_agenda.id_agenda',
    //                         'historico_agenda.id_status',
    //                         'historico_agenda.id_tipo_procedimento',
    //                         'historico_agenda.id_procedimento',
    //                         'historico_agenda.id_tipo_confirmacao',
    //                         'historico_agenda.campo',
    //                         'agenda_confirmacao.descr',
    //                         'tipo_procedimento.descr',
    //                         'pessoa.nome_fantasia',
    //                         'agenda_status.descr',
    //                         'historico_agenda.created_at',
    //                         'historico_agenda.created_by'
    //                     )
    //                     ->orderby('historico_agenda.created_at')
    //                     ->get();

    //         return json_encode($historico);
    //     } catch (\Exception $e) {
    //         return $e->getMessage();
    //     } 
    // }



}