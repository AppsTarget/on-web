<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\Pedido;
use App\Pessoa;
use App\TabelaPrecos;
use App\Modalidades_por_plano;
use App\Comissao_exclusiva;
use Illuminate\Http\Request;

class RelatorioAtividadesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $empresas =  DB::table('empresa')
                     ->get();
        return view('relatorio_atividades', compact('empresas'));
    }
    public function listar_contratos(Request $request) {
        return DB::table('pedido')
                ->select('pedido.id', 'pedido.data', DB::raw('GROUP_CONCAT(distinct tabela_precos.descr) AS descr'))
                ->leftjoin('pedido_planos', 'pedido_planos.id_pedido', 'pedido.id')
                ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                ->where('pedido.id_paciente', $request->id_paciente)
                ->where('pedido.status', 'F')
                ->where('pedido.lixeira', 0)
                ->where(function($sql) use ($request) {
                    if ($request->datainicial != '') {
                        $sql->where('pedido.data', '>=', $request->datainicial);
                    }
                    if ($request->datafinal != '') {
                        $sql->where('pedido.data', '<=', $request->datafinal);
                    }
                })
                ->groupBy('pedido.id', 'pedido.data')
                ->get();
    }
    public function listar_contratos_antigos(Request $request) {
        return DB::table('old_contratos')
               ->select('old_contratos.id', 'old_contratos.datainicial as data', DB::raw('GROUP_CONCAT(distinct old_modalidades.descr) AS descr'))
               ->leftjoin('old_atividades', 'old_atividades.id_contrato', 'old_contratos.id')
               ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
               ->where('old_contratos.pessoas_id', $request->id_paciente)
               ->where('old_contratos.situacao', '1')
               ->where(function($sql) use ($request) {
                    if ($request->datainicial != '') {
                        $sql->where('old_contratos.datainicial', '>=', $request->datainicial);
                    }
                    if ($request->datainicial != '') {
                        $sql->where('old_contratos.datainicial', '<=', $request->datafinal);
                    } 
               })
               ->groupBy("old_contratos.id", "old_contratos.datainicial")
               ->orderBy('data', 'DESC')
               ->get();
    }

    public function listar_planos($id) {
        return DB::table('pedido_planos')
                ->select('tabela_precos.id', 'tabela_precos.descr')
                ->leftjoin('tabela_precos', 'tabela_precos.id', 'pedido_planos.id_plano')
                ->where('pedido_planos.id_pedido', $id)
                ->get();
    }

    public function listar_planos_antigo($id) {
        return DB::table('old_atividades')
                ->select('old_atividades.id', 'old_modalidades.descr')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->where('old_atividades.id_contrato', $id)
                ->get();
    }

    public function imprimir($antigo, $id_contrato, $id_plano){
        switch($antigo) {
            case 0:
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
                                'pessoa.nome_fantasia as nome_paciente',
                                'agenda.id_tabela_preco',
                                'agenda.id_pedido',
                                DB::raw(
                                    'CONCAT((CASE' .
                                    '   WHEN profissional.nome_reduzido IS NOT NULL THEN profissional.nome_reduzido ' .
                                    '   ELSE profissional.nome_fantasia ' .
                                    "END), ' - ', empresa.descr) AS nome_profissional"
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
                                'tabela_precos.descr As descr_plano',
                                DB::raw("CASE WHEN agenda.obs IS NOT NULL THEN CONCAT('OBS.: ', agenda.obs) ELSE '' END AS obs"),
                                DB::raw("(select 0) AS antigo")
                                )
                                ->leftjoin('empresa', 'empresa.id', 'agenda.id_emp')
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
                                ->leftjoin('tabela_precos', 'tabela_precos.id', 'agenda.id_tabela_preco')
                                ->where('agenda.lixeira', 0)
                                ->where('id_tipo_procedimento', '<>', 5)
                                ->whereRaw("(agenda.status in ('F', 'A'))")
                                ->where('agenda.id_pedido', $id_contrato)
                                ->where(function($sql) use($id_plano) {
                                    if ($id_plano != 0) {
                                        $sql->where('agenda.id_tabela_preco', $id_plano);
                                    }
                                })
                                ->orderBy('agenda.id_tabela_preco')
                                ->orderBy('agenda.data', 'ASC')
                                ->orderBy('agenda.hora', 'ASC')
                                ->get();
                $sql = "select 
                            SUM(pedido_planos.qtde * pedido_planos.qtd_total) As total
                        from 
                            pedido_planos 
                        where id_pedido = " . $id_contrato;
                if ($id_plano <> 0) {
                    $sql += " AND id_plano = " . $id_plano;
                }
                $total_atividades  = DB::select(DB::raw($sql));
                // return $agendamentos;

                $pedido_header = DB::table('pedido')
                        ->select(
                            'pedido.id',
                            'pedido.id AS num_pedido',  
                            'pedido.status',
                            'pedido.data_validade',
                            'pedido.obs',
                            'paciente.nome_fantasia AS descr_paciente',
                            'prof_examinador.nome_fantasia AS descr_prof_exa',
                            'convenio.descr AS descr_convenio',
                            'pedido.data'
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'pedido.id_paciente')
                        ->leftjoin('pessoa AS prof_examinador', 'prof_examinador.id', 'pedido.id_prof_exa')
                        ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                        ->where('pedido.id', $id_contrato)
                        ->first();
                // return $agendamentos;
                return view('reports.impresso_atividades', compact('agendamentos', 'planos', 'pedido_header', 'total_atividades'));
                break;
            case 1:
                $agendamentos = DB::table('old_mov_atividades')
                ->select("old_mov_atividades.id",
                         DB::raw("(select 1) AS id_emp"),
                         "old_mov_atividades.id_status",
                         "old_mov_atividades.status",
                         "old_mov_atividades.id_tipo_procedimento",
                         "tipo_procedimento.descr AS tipo_procedimento",
                         "old_mov_atividades.id_grade AS id_grade_horario",
                         "old_mov_atividades.hora",
                         "old_mov_atividades.data",
                         "old_contratos.pessoas_id AS id_paciente",
                         "pessoa.nome_fantasia AS nome_paciente",
                         "profissional.nome_fantasia AS nome_profissional",
                         "old_modalidades.descr AS descr_procedimento",
                         "old_financeira.descr AS convenio_nome",
                         "old_modalidades.descr As descr_plano",
                         "agenda_status.permite_editar",
                         "agenda_status.libera_horario",
                         "agenda_status.permite_fila_espera",
                         "agenda_status.permite_reagendar",
                         "agenda_status.descr AS descr_status",
                         "agenda_status.cor AS cor_status",
                         "agenda_status.cor_letra",
                         DB::raw("(select 0) AS min_intervalo"),
                         DB::raw("(select 0) AS id_confirmacao"),
                         DB::raw("(select '') AS descr_confirmacao"),
                         DB::raw("(select '') AS obs"), DB::raw("(select 1) AS antigo"))
                ->leftjoin('old_atividades', 'old_atividades.id', 'old_mov_atividades.id_atividade')
                ->leftjoin('old_modalidades', 'old_modalidades.id', 'old_atividades.id_modalidade')
                ->leftjoin('agenda_status', 'agenda_status.id', 'old_mov_atividades.id_status')
                ->leftjoin('old_contratos', 'old_contratos.id', 'old_atividades.id_contrato')
                ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_atividades.id_contrato')
                ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_financeira')
                ->leftjoin('tipo_procedimento', 'tipo_procedimento.id', 'old_mov_atividades.id_tipo_procedimento')
                ->leftjoin('pessoa', 'pessoa.id', 'old_contratos.pessoas_id')
                ->leftjoin('pessoa AS profissional', 'profissional.id', 'old_mov_atividades.id_membro')
                ->where('old_mov_atividades.lixeira', 0)
                ->where('old_atividades.id_contrato', $id_contrato)
                ->whereRaw(" (old_mov_atividades.status in ('F', 'A'))")
                ->where(function($sql) use($id_plano) {
                    if($id_plano != 0) {
                        $sql->where('old_atividades.id_atividade', $id_plano);
                    }
                })
                ->groupBy("old_mov_atividades.id",
                         "old_mov_atividades.id_status",
                         "old_mov_atividades.status",
                         "old_mov_atividades.id_tipo_procedimento",
                         "tipo_procedimento.descr",
                         "old_mov_atividades.id_grade",
                         "old_mov_atividades.hora",
                         "old_mov_atividades.data",
                         "old_contratos.pessoas_id",
                         "pessoa.nome_fantasia",
                         "profissional.nome_fantasia",
                         "old_modalidades.descr",
                         "old_financeira.descr",
                         "agenda_status.permite_editar",
                         "agenda_status.libera_horario",
                         "agenda_status.permite_fila_espera",
                         "agenda_status.permite_reagendar",
                         "agenda_status.descr",
                         "agenda_status.cor",
                         "agenda_status.cor_letra"
                         )
                ->orderBy('old_modalidades.descr')
                ->orderBy('old_mov_atividades.data', 'DESC')
                ->orderBy('old_mov_atividades.hora', 'DESC')
                ->get();

                $pedido_header = DB::table('old_contratos')
                        ->select(
                            DB::raw('old_contratos.id AS id'),
                            DB::raw('old_contratos.id AS num_pedido'),
                            DB::raw("CASE WHEN (old_contratos.situacao = 1) THEN (select 'F')
                                          ELSE old_contratos.situacao END AS status"),
                            DB::raw("CONCAT(datafinal, ' ', horafinal) AS data_validade"),
                            DB::raw("(select 'sistema antigo') AS obs"),
                            'paciente.nome_fantasia AS descr_paciente',
                            DB::raw(" CASE WHEN (old_contratos.tipo_contrato = 'P') THEN (
                                        SELECT 
                                            usu_confirm 
                                        FROM 
                                            old_mov_atividades 
                                            INNER JOIN old_atividades ON old_mov_atividades.id_atividade = old_atividades.id 
                                        WHERE 
                                            old_atividades.id_contrato = old_contratos.id limit 1
                                        ) ELSE (
                                        SELECT 
                                            old_contratos.responsavel
                                        ) END AS descr_prof_exa"),
                            "old_financeira.descr AS descr_convenio",
                            "old_contratos.datainicial AS data"
                        )
                        ->leftjoin('pessoa AS paciente', 'paciente.id', 'old_contratos.pessoas_id')
                        ->leftjoin('old_finanreceber', 'old_finanreceber.id_contrato', 'old_contratos.id')
                        ->leftjoin('old_financeira', 'old_financeira.id', 'old_finanreceber.id_planopagamento')
                        ->where('old_contratos.id', $id_contrato)
                        ->first();
                
                        
                return view('reports.impresso_atividades', compact('agendamentos', 'planos', 'pedido_header', 'total_atividades'));
                break;
        }
    }
}