<?php

function getProfissional() {
    return DB::table('pessoa')->where('id', Auth::user()->id_profissional)->first();
}

function getEmpresa()
{
    return Auth::user()->id_emp;
}
function getAssociadoRegra() {
    return DB::table('associados_regra')
           ->where('ativo', true)
           ->value('dias_pos_fim_contrato');
}
function gerar_num() {
    $num_pedido = DB::table('pedido')->max('num_pedido');
    if ($num_pedido == '') $num_pedido = 1;
    else                   $num_pedido++;
    return $num_pedido;
}
function getCaixa() {
    return DB::table('caixa')
            ->select(DB::raw('caixa.*'))
            ->leftjoin('caixa_operadores', 'caixa_operadores.id_caixa', 'caixa.id')
            ->where('caixa.id_emp', getEmpresa())
            ->where('caixa_operadores.id_operador', Auth::user()->id_profissional)
            ->where('lixeira', 0)
            ->where('ativo', 'S')
            ->orderBy('created_at', 'DESC')
            ->first();
}
function gerar_id(){
    $novo_id = DB::table('pedido')->max("id");
    if($novo_id == '') $novo_id = 1;
    return $novo_id;
}
function ZapSignApi() {
    return 'https://api.zapsign.com.br/api/v1/';
}
function ZEnviaApi() {
    return "https://api.zenvia.com/v2/";
}
function ZapSignToken() {
    return 'c592ff40-556c-48aa-b59b-1623831c8be8ac686611-df03-4aab-b61c-b372b6efceb6';
}
function ModeloDocId() {
    return 'c3ac6de4-f745-447e-8940-668471024062';
}
function getParamAgendaContrato($id_agendamento){
    return DB::table("agenda")
           ->join('tipo_procedimento', 'tipo_procedimento.id', 'agenda.id_tipo_procedimento')
           ->where('agenda.id', $id_agendamento)
           ->max('tipo_procedimento.assossiar_contrato');
}
function getParamAgendaPlano($id_agendamento){
    return DB::table('agenda')
           ->join("tabela_precos", 'tabela_precos.id', 'agenda.id_tabela_preco')
           ->where('agenda.id', $id_agendamento)
           ->value('tabela_precos.repor_som_mes');
}
function validar_status_reagendar(){
    $agenda_status = DB::table('agenda_status')
                    ->where('caso_reagendar', true)
                    ->where('lixeira', false)
                    ->get();
    if (sizeof($agenda_status) > 0) return false;
    else                            return sizeof($agenda_status);
}
function validar_status_confirmar(){
    $agenda_status = DB::table('agenda_status')
                     ->where('caso_confirmar', true)
                     ->where('lixeira', false)
                     ->get();
    if (sizeof($agenda_status) > 0) return false;
    else                            return true;
}
function getVigenciaPlano($id_pedido) {
    $pedido = DB::table('pedido_planos')
            ->select('tabela_precos.vigencia')
            ->join('tabela_precos', 'tabela_precos.id', 'id_plano')
            ->where('pedido_planos.id_pedido', $id_pedido)
            ->max('tabela_precos.vigencia');
    return $pedido;
}
function getMaxAtvSemana($id_plano){
    $saldo = DB::table('tabela_precos')
             ->select('max_atv_semana')
             ->where('id', $id_plano)
             ->max('max_atv_semana');
    return $saldo;
}

function getEmpresaObj()
{
    return DB::table('empresa')->where('id', Auth::user()->id_emp)->first();
}

function mostrar_especialidades($id_profissional) {
    return DB::table('especialidade_pessoa')
            ->select(
                'especialidade.id',
                'especialidade.descr'
            )
            ->leftjoin('especialidade', 'especialidade.id', 'especialidade_pessoa.id_especialidade')
            ->where('especialidade_pessoa.id_profissional', $id_profissional)
            ->get();
}

function statusCasoConfirmar(){
    return DB::table("agenda_status")
        //    ->where('id_emp', getEmpresa())
           ->where('caso_confirmar', true)
           ->where('lixeira', false)
           ->first();
}
function getAge($date) {
    return intval(date('Y', time() - strtotime($date))) - 1970;
}
function statusCasoReagendar(){
    return DB::table('agenda_status')
            // ->where('id_emp', getEmpresa())
            ->where('caso_reagendar', true)
            ->orderby('updated_at', 'DESC')
            ->first();
}
function statusCasoCancelar() {
    return DB::table('agenda_status')
            // ->where('id_emp', getEmpresa())
            ->where('caso_cancelar', true)
            ->orderBy('updated_at', 'DESC')
            ->first();
}

function getSaldoConta($id_conta) {
    $mov = DB::table('mov_conta')
           ->select('mov_conta.tipo', 'mov_conta.valor')
           ->leftjoin('titulos_receber', 'titulos_receber.id', 'mov_conta.id_titulo')
           ->leftjoin('titulos_pagar',   'titulos_pagar.id',   'mov_conta.id_titulo')
           ->leftjoin('pedido', 'pedido.id', 'titulos_receber.id_pedido')
           ->get();
}