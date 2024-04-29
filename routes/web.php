<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/',                   'HomeController@index');
Route::get('/home',               'HomeController@index');
Route::get('/administrador',      'HomeController@administrador');
Route::get('/parametros',         'HomeController@parametros');
Route::get('/autocomplete',       'HomeController@autocomplete');
Route::get('/autocompleteagenda', 'HomeController@autocomplete_agenda');


Route::group(['prefix'=>'email'], function() {
    Route::get('/enviar', 'EmailController@enviarEmail');
});

Route::group(['prefix'=>'agenda'], function() { 
    Route::get ('/',                                                            'AgendaController@mostrar');
    Route::get ('/listar-agendamentos',                                         'AgendaController@listar_agendamentos');
    Route::get ('/listar-agendamentos-semanal',                                 'AgendaController@listar_agendamentos_semanal');
    Route::get ('/listar-agendamentos-colab',                                   'AgendaController@listar_agendamentos_colab');
    Route::post('/salvar',                                                      'AgendaController@salvar');
    Route::get ('/confirmar-agendamento/{id}',                                  'AgendaController@confirmar_agendamento');
    Route::post('/confirmar-agendamento-mobile',                                'AgendaController@confirmar_agendamento_mobile');
    Route::post('/finalizar-agendamento',                                       'AgendaController@finalizar_agendamento');
    Route::post('/cancelar-agendamento',                                        'AgendaController@cancelar_agendamento');
    Route::get ('/agendamento-info/{id}',                                       'AgendaController@agendamento_info');
    Route::get ('/mostrar-agendamento/{id}',                                    'AgendaController@mostrar_agendamento');
    Route::get ('/verificar-grade',                                             'AgendaController@verificar_grade');
    Route::get ('/pesquisar-agendamento',                                       'AgendaController@pesquisar_agendamento');
    Route::post('/mudar-status',                                                'AgendaController@mudar_status');
    Route::post('/mudar-tipo-confirmacao',                                      'AgendaController@mudar_tipo_confirmacao');
    Route::post('/copiar-agendamento',                                          'AgendaController@copiar_agendamento');
    Route::post('/copiar-agendamento-id',                                       'AgendaController@copiar_agendamento_id');
    Route::get('/deletar',                                                      'AgendaController@deletar');
    Route::get ('/agendamentos-pessoa/{id_pessoa}',                             'AgendaController@agendamentosPorPessoa');
    Route::get ('/atividade-semanal/{id_pessoa}',                               'AgendaController@listar_atividades_semanais');
    Route::get ('/faturar/{id_agendamento}',                                    'AgendaController@faturar');
    Route::get('/validar-plano',                                                'AgendaController@validar_plano_semana');
    Route::post('/salvar-faturamento',                                          'AgendaController@salvar_faturamento');
    Route::get ('/verificar_confirmado/{id}',                                   'AgendaController@verificar_confirmado');
    Route::get ('/editar_agendamento_/{id}',                                    'AgendaController@editar_agendamento');
    Route::get ('/expandir-agendamento/{id}/{sistema_antigo}',                  'AgendaController@expandir_agendamento');
    Route::get ('/modal-agendamento-lote/{paciente}/{contrato}/{plano}',        'AgendaController@modal_agendamento_lote');
    Route::post('/gerar-agendamentos-em-lote',                                  'AgendaController@gerar_agendamentos_em_lote');
    Route::get ('/listar_profissionais_lote',                                   'AgendaController@listar_profissionais_lote');
    Route::get ('/listar-planos-desc/{id}/{id_pessoa}',                         'AgendaController@listar_planos_desc');
    Route::get ('/listar-planos-desc2/{id}/{id_pessoa}/{id_profissional}',      'AgendaController@listar_planos_desc2');
    Route::post ('/salvar_op_bordero',                                          'AgendaController@salvar_op_bordero');
    Route::get('/listar-todos-agendamento-semanal',                             'AgendaController@listar_todos_agendamentos_semanal');
    Route::get('/expandir-agendamento-view',                                    'AgendaController@expandir_agendamento_view');
    Route::get ('/listar-modalidades-por-plano/{id}',                           'AgendaController@listar_modalidades_por_plano');
    Route::get('/agendamentos-pendentes',                                       'AgendaController@listar_agendamentos_pendentes');
    Route::get('/imprimir/{dinicial}/{dfinal}/{id_membro}/{status}/{completo}', 'AgendaController@imprimir');
    Route::post('/salvar-lote',                                                  'AgendaController@salvarLote');

    Route::get('/notificar_participantes',                                      'AgendaController@notificar_participantes');
});

Route::group(['prefix'=>'agenda-status'], function() {
    Route::get ('/',                                'AgendaStatusController@mostrar');
    Route::get ('/salvar',                          "AgendaStatusController@salvar");
    Route::get ('/deletar',                         "AgendaStatusController@deletar");

//     Route::get ('/listar-agendamentos',             'AgendaController@listar_agendamentos');
//     Route::get ('/listar-agendamentos-semanal',     'AgendaController@listar_agendamentos_semanal');
//     Route::get ('/listar-agendamentos-colab',       'AgendaController@listar_agendamentos_colab');
//     Route::post('/salvar',                          'AgendaController@salvar');
//     Route::post('/confirmar-agendamento',           'AgendaController@confirmar_agendamento');
//     Route::post('/finalizar-agendamento',           'AgendaController@finalizar_agendamento');
//     Route::post('/cancelar-agendamento',            'AgendaController@cancelar_agendamento');
//     Route::get ('/agendamento-info/{id}',           'AgendaController@agendamento_info');
//     Route::get ('/verificar-grade',                 'AgendaController@verificar_grade');
//     Route::get ('/pesquisar-agendamento',           'AgendaController@pesquisar_agendamento');
//     Route::post('/mudar-status',                    'AgendaController@mudar_status');
//     Route::post('/mudar-tipo-confirmacao',          'AgendaController@mudar_tipo_confirmacao');
//     Route::post('/copiar-agendamento',              'AgendaController@copiar_agendamento');
//     Route::post('/copiar-agendamento-id',           'AgendaController@copiar_agendamento_id');
//     Route::post('/deletar',                         'AgendaController@deletar');
//     Route::get ('/agendamentos-pessoa/{id_pessoa}', 'AgendaController@agendamentosPorPessoa');
// });
});

Route::group(['prefix' => 'pessoa'], function() {
    Route::get('/alterar-empresa',                       'PessoaController@alterar_empresa');
    Route::get('/empresa',                               'PessoaController@empresa');
    Route::get('/listar-empresas',                       'PessoaController@listar_empresas');
    Route::get ('/max-cod-interno',                       'PessoaController@max_cod_interno');
    Route::post('/salvar',                                'PessoaController@salvar');
    Route::post('/inativar',                              'PessoaController@inativar');
    Route::get ('/listar',                                'PessoaController@listar');
    Route::get ('/listar-paciente',                       'PessoaController@psqPaciente');
    Route::get ('/verificar-associado/{id}',              'PessoaController@verificar_associado');
    Route::get ('/mostrar/{id_pessoa}',                   'PessoaController@mostrar');
    Route::get ('/prontuario/{id_pessoa}',                'PessoaController@abrir_prontuario');
    Route::get ('/resumo-pessoa/{id_pessoa}',             'PessoaController@resumoPorPessoa');
    Route::get ('/resumo-corpo/{id_pessoa}/{id_corpo}',   'PessoaController@resumoPorParteCorpo');
    Route::get ('/verificar-pre-cadastro/{id_paciente}',  'PessoaController@verificar_pre_cadastro');
    Route::get ('/listar-corpo',                          'PessoaController@listar_corpo_json');
    Route::get ('/verificar-admin',                       'PessoaController@verificar_adm');
    Route::get ('/verificar-admin-agenda/{id}',           'PessoaController@verificar_adm_agenda');
    Route::get ('/verificar-admin-agenda2',                'PessoaController@verificar_adm_agenda2');
    Route::get ('/ver-usuario',                            'PessoaController@ver_usuario');
    Route::get ('/retornar-usuario',                       'PessoaController@retornar_usuario');
    Route::post('/atualizar-cadastro-contrato',            'PessoaController@atualizar_cadastro_contrato');
    Route::get (    
        '/status-evolucao/{id_pessoa}/{id_corpo}',                     
        'PessoaController@status_evolucao'
    );
    Route::get (
        '/resumo-vitruviano/{id_pessoa}/{id_corpo}',                     
        'PessoaController@resumo_vitruviano'
    );
    Route::get('/listar-membros',                                "PessoaController@listar_membros");
    Route::get('/listar-membros-e-horarios',                     "PessoaController@listar_membros_e_horarios");

    Route::get('verificar-duplicidade/{cpf}/{id_pessoa}',    'PessoaController@verificar_duplicidade');
    Route::get("/get-nome/{id_pessoa}", "PessoaController@getNome");
    Route::get("/atividades/{id}", "PessoaController@atividades");
});

Route::group(['prefix'=>'cliente'], function() {
    Route::get ('/', 'PessoaController@listarClientes');
});

Route::group(['prefix'=>'profissional'], function() {
    Route::get ('/{filtro}', 'PessoaController@listarProfissionais');
});

Route::group(['prefix'=>'paciente'], function() {
    Route::get ('/', 'PessoaController@listarPacientes');
});

Route::group(['prefix'=>'atendimento'], function() {
    Route::post('/comecar-atendimento',                      'AtendimentoController@comecar_atendimento');
    Route::post('/parar-atendimento',                        'AtendimentoController@parar_atendimento');
    Route::get ('/paciente-em-aberto/{id_paciente}',         'AtendimentoController@paciente_em_aberto');
    Route::get ('/profissional-em-aberto/{id_profissional}', 'AtendimentoController@profissional_em_aberto');
});

Route::group(['prefix'=>'grade'], function() {
    Route::get ('/mostrar-pessoa/{id_pessoa}/{id_emp}',  'GradeController@mostrar_pessoa');
    Route::post('/salvar',                               'GradeController@salvar');
    Route::post('/deletar',                              'GradeController@deletar');
    Route::post('/ativar-desativar',                     'GradeController@ativar_desativar');
    Route::post('/dividir-horario',                      'GradeController@dividir_horario');
    Route::post('/dividir-horario-por-id',               'GradeController@dividir_horario_por_id');
    Route::get ('/verificar-grade-por-horario',          'GradeController@verificar_grade_por_horario');
    Route::get ('/mostrar-grade-por-horario',            'GradeController@mostrar_grade_por_horario');
    Route::get ('/verificar-grade-por-semana',           'GradeController@verificar_grade_por_dia_semana');
    Route::get ('/listar-todos-horarios',                'GradeController@listar_todos_horarios');
});

Route::group(['prefix'=>'grade-bloqueio'], function() {
    Route::get ('/mostrar-pessoa/{id_pessoa}', 'GradeBloqueioController@mostrar_pessoa');
    Route::post('/salvar',                     'GradeBloqueioController@salvar');
    Route::post('/ativar-desativar',           'GradeBloqueioController@ativar_desativar');
    Route::post('/deletar',                    'GradeBloqueioController@deletar');
});

Route::group(['prefix'=>'procedimento'], function() {
    Route::get ('/',                            'ProcedimentoController@listar');
    Route::get ('/listar',                      'ProcedimentoController@listar_json');
    Route::get ('/mostrar/{id}',                'ProcedimentoController@mostrar');
    Route::post('/salvar',                      'ProcedimentoController@salvar');
    Route::post('/deletar',                     'ProcedimentoController@deletar');
    Route::get('/verificar-convenio',           'ProcedimentoController@verificar_convenio');
    Route::post('/adicionar-metas',             'ProcedimentoController@adicionar_metas');
    Route::get('/listar-metas/{id}',            'ProcedimentoController@listar_metas');
    Route::post('/excluir-meta',                'ProcedimentoController@excluir_meta');
    Route::post('/salvar-metas-modalidade',     'ProcedimentoController@salvar_metas_modalidade');
});

Route::group(['prefix'=>'especialidade'], function() {
    Route::get ('/',             'EspecialidadeController@listar');
    Route::get ('/listar',       'EspecialidadeController@listar_json');
    Route::get ('/mostrar/{id}', 'EspecialidadeController@mostrar');
    Route::post('/salvar',       'EspecialidadeController@salvar');
    Route::post('/deletar',      'EspecialidadeController@deletar');
});

Route::group(['prefix'=>'prescricao'], function() {
    Route::get ('/imprimir/{id}',             'PrescricaoController@imprimir');
    Route::get ('/mostrar/{id}',              'PrescricaoController@mostrar');
    Route::post('/salvar',                    'PrescricaoController@salvar');
    Route::post('/deletar',                   'PrescricaoController@deletar');
    Route::get ('/listar-pessoa/{id_pessoa}', 'PrescricaoController@listarPorPessoa');
});

Route::group(['prefix'=>'anexos'], function() {
    Route::get ('/listar',                            'AnexosController@listar');
    Route::post('/salvar',                            'AnexosController@salvar');
    Route::post('/deletar',                           'AnexosController@deletar');
    Route::get ('/baixar/{id}',                       'AnexosController@baixar');
    Route::get ('/listar-pessoa/{id_pessoa}/{pasta}', 'AnexosController@listarPorPessoa');
});

Route::group(['prefix'=>'pastas'], function() {
    Route::post('/criar', 'PastasController@criar');
});

Route::group(['prefix'=>'fila-espera'], function() {
    Route::get ('/{id_profissional}','FilaEsperaController@listar_profissional');
    Route::get ('/listar',        'FilaEsperaController@listar');
    Route::post('/salvar',        'FilaEsperaController@salvar');
    Route::post('/atender-fila',  'FilaEsperaController@atender_fila');
    Route::post('/desistir-fila', 'FilaEsperaController@desistir_fila');
    Route::post('/confirmar',     'FilaEsperaController@confirmar');
});

Route::group(['prefix'=>'evolucao'], function() {
    Route::post('/salvar',                   'EvolucaoController@salvar');
    Route::post('/deletar',                  'EvolucaoController@deletar');
    Route::get ('/listar/{id_paciente}',     'EvolucaoController@listar');
    Route::get ('/mostrar/{id}',             'EvolucaoController@mostrar');
    Route::get ('/listar-pessoa/{id_pessoa}','EvolucaoController@listarPorPessoa');
    Route::post('/tornar-privado',           'EvolucaoController@tornar_privado');
    Route::post('/tornar-publico',           'EvolucaoController@tornar_publico');
    Route::get ('/listar-agendamentos/{id}', 'EvolucaoController@listarAgendamentos');
    Route::get ('/listar-agendas/{id_pessoa}', 'EvolucaoController@listarAgendas');

});

Route::group(['prefix'=>'evolucao-pedido'], function() {
    Route::post('/salvar',                      'EvolucaoPedidoController@salvar');
    Route::post('/deletar',                     'EvolucaoPedidoController@deletar');
    Route::get ('/listar/{id_pedido_servicos}', 'EvolucaoPedidoController@listar');
});

Route::group(['prefix'=>'evolucao-tipo'], function() {
    Route::get ('/',             'EvolucaoTipoController@listar');
    Route::get ('/listar',       'EvolucaoTipoController@listar_json');
    Route::post('/salvar',       'EvolucaoTipoController@salvar');
    Route::post('/deletar',      'EvolucaoTipoController@deletar');
    Route::get ('/mostrar/{id}', 'EvolucaoTipoController@mostrar');
});

Route::group(['prefix'=>'convenio'], function() {
    Route::get ('/',                           'ConvenioController@listar');
    Route::get ('/listar',                     'ConvenioController@listar_json');
    Route::get ('/mostrar/{id}',               'ConvenioController@mostrar');
    Route::post('/salvar',                     'ConvenioController@salvar');
    Route::post('/inativar',                   'ConvenioController@inativar');
    Route::post('/adicionar-valor-por-plano',  'ConvenioController@adicionar_valor_por_plano');
    Route::post('/criar-convenio',             'ConvenioController@criar_convenio');
    Route::post('/remover-preco-convenio',     'ConvenioController@remover_preco_convenio');
    Route::get('/verificar-carteira',          'ConvenioController@verificar_carteira');
});

Route::group(['prefix'=>'tabela-precos'], function() {
    Route::get ('/',                                        'TabelaPrecosController@listar');
    Route::get ('/mostrar/{id}',                            'TabelaPrecosController@mostrar');
    Route::get ('/listar-empresas/{id}',                    'TabelaPrecosController@listar_empresas');
    Route::post('/salvar',                                  'TabelaPrecosController@salvar');
    Route::post('/deletar',                                 'TabelaPrecosController@deletar');
    Route::post('/clonar-precos',                           'TabelaPrecosController@clonar_precos');
    Route::get ('/listar_tabela/{id_tabela_precos}',        'TabelaPrecosController@listar_tabela_modalidades');
    Route::post('/salvarModalidade',                        'TabelaPrecosController@salvarModalidade');
    Route::post('/deletarModalidade',                       'TabelaPrecosController@deletarModalidade');
    Route::get ('/mostrarModalidade/{id_modalidade}',       'TabelaPrecosController@mostrarModalidade');
    Route::get ('/procurar-modalidades/{id}',               'TabelaPrecosController@procurar_modalidades');
    Route::get ('/listar-modalidades/{id}',                 'TabelaPrecosController@listar_modalidades');
    Route::post('/adicionar-vigencia-plano',                'TabelaPrecosController@add_vigencia_plano');
    Route::post('/excluir-vigencia-plano',                  'TabelaPrecosController@excluir_vigencia_plano');
    Route::get('/listar-vigencia-plano/{id}',               'TabelaPrecosController@listar_vigencias_plano');
});
Route::group(['prefix'=>'parametros'], function() {
    Route::post('/salvar-desconto-geral',                  'ParametrosController@salvar_desconto_geral');
    Route::get('/mostrar-param-atual',                     'ParametrosController@mostrar_param_atual');
});

Route::group(['prefix'=>'convenio-preco'], function() {
    Route::post('/salvar',               'ConvenioPrecoController@salvar');
    Route::post('/inativar',             'ConvenioPrecoController@inativar');
    Route::get ('/listar/{id_convenio}', 'ConvenioPrecoController@listar');
});
Route::group(['prefix'=>'comissao_exclusiva'], function(){
    Route::post('/salvar',                           'Comissao_exclusivaController@salvar');
    Route::get ('/listar_tabela/{id_tabela_precos}', 'Comissao_exclusivaController@listar_tabela');
    Route::get ('/mostrar/{id_preco}',               'Comissao_exclusivaController@mostrar');
    Route::post('/deletar',                          'Comissao_exclusivaController@deletar');
});

Route::group(['prefix'=>'preco'], function() {
    Route::get ('/listar_tabela/{id_tabela_precos}', 'PrecoController@listar_tabela');
    Route::get ('/mostrar/{id_preco}',               'PrecoController@mostrar');
    Route::post('/salvar',                           'PrecoController@salvar');
    Route::post('/deletar',                          'PrecoController@deletar');
});

Route::group(['prefix'=>'grade-horario'], function() {
    Route::get ('/grade/{dia_semana}',    'GradeHorarioController@listar');
});

Route::group(['prefix'=>'forma-pag'], function() {
    Route::get ('/',             'FormaPagController@listar');
    Route::get ('/listar/{tipo}','FormaPagController@listar_tipo');
    Route::get ('/mostrar/{id}', 'FormaPagController@mostrar');
    Route::get ('/consulta_descr/{descr}', 'FormaPagController@consulta_descr');
    Route::post('/salvar',       'FormaPagController@salvar');
    Route::post('/deletar',      'FormaPagController@deletar');
});

Route::group(['prefix'=>'financeira'], function() {
    Route::get ('/',             'FinanceiraController@listar');
    Route::get ('/mostrar/{id}', 'FinanceiraController@mostrar');
    Route::get('/salvar',       'FinanceiraController@salvar');
    Route::post('/deletar',      'FinanceiraController@deletar');
});

Route::group(['prefix'=>'financeira-taxas'], function() {
    Route::get ('/listar/{id_financeira}', 'FinanceiraTaxasController@listar');
    Route::get ('/mostrar/{id}',           'FinanceiraTaxasController@mostrar');
    Route::post('/salvar',                 'FinanceiraTaxasController@salvar');
    Route::post('/deletar',                'FinanceiraTaxasController@deletar');
});

Route::group(['prefix'=>'financeira-formas-pag'], function() {
    Route::get ('/listar/{id_forma_pag}',   'FinanceiraFormasPagController@listar');
    Route::get ('/mostrar/{id}',            'FinanceiraFormasPagController@mostrar');
    Route::get ('/listar-financeiras/{id}', 'FinanceiraFormasPagController@listar_financeiras');

    Route::post('/salvar',                  'FinanceiraFormasPagController@salvar');
    Route::post('/deletar',                 'FinanceiraFormasPagController@deletar');
});

Route::group(['prefix'=>'pedido'], function() {
    Route::get ('/',                                    'PedidoController@listar');
    Route::get ('/mostrar/{id_orcamento}',              'PedidoController@mostrar');
    Route::get ('/listar-planos/{id}/{id_pessoa}',      'PedidoController@listar_planos');
    Route::get ('/gerar-num',                           'PedidoController@gerar_num');
    Route::post('/adicionar-plano',                     'PedidoController@adicionar_plano');
    Route::get('/salvar',                              'PedidoController@salvar');
    Route::post('/mudar-status',                        'PedidoController@mudar_status');
    Route::post('/deletar',                             'PedidoController@deletar');
    Route::post('/limparplanos',                        'PedidoController@limpar_planos');
    Route::get('/deletar-plano/{id}',                   'PedidoController@deletar_plano');
    Route::get ('/imprimir/{id}/{antigo}',              'PedidoController@imprimir');
    Route::post('/conversao-plano',                     'PedidoController@conversao_plano');
    Route::get ('/listar-pessoa/{id_pessoa}/{antigo}',  'PedidoController@listarPorPessoa');
    Route::get ('/listar-contratos-pessoa/{id}/{op}/{data}',   'PedidoController@listar_contratos_pessoa');
    Route::get ('/listar-planos-pedido',                'PedidoController@listar_planos_pedido');
    Route::get ('/listar-planos-pessoa/{id}',           'PedidoController@listar_planos_pessoa');
    Route::get('/montar-resumo',                       'PedidoController@montar_resumo');
    Route::post('/congelar',                            'PedidoController@congelar_pedido');
    Route::post('/descongelar',                         'PedidoController@descongelar');
    Route::get('/atividades-por-pessoa/{id}',           'PedidoController@atividades_por_pessoa');
    Route::get('/agendamentos-por-pessoa/{id}',         'PessoaController@agendados_atividade_modal');
    Route::get ('/validar/{id_pessoa}',                 'PedidoController@validar');
    Route::get ('/listar-planos-desc/{id_p}/{id_conv}', 'PedidoController@listar_planos_desc');
    Route::get('/enviar-por-email',                     'PedidoController@enviar_contrato_por_email');
    Route::get('/abrir-modal-conversao/{id}/{antigo}',  'PedidoController@abrir_modal_conversao');
    Route::get('/converter',                             'PedidoController@converter');
    Route::get('/listar-mov-credito/{id}/{dini}/{dfim}', 'PedidoController@listar_mov_credito');
    Route::get('/creditos-restantes/{id}',               'PedidoController@mostrar_creditos_restantes');
    Route::get('/filtrar-pesquisa',                      'PedidoController@filtrar_pesquisa');
    Route::post('/validar-supervisor',                   'PedidoController@validarSupervisor');
    Route::get('/log/{id}',                              'PedidoController@log');
    Route::get('/atividades/{id}',                       'PedidoController@atividades');
});
Route::group(['prefix' =>'contratos'], function () {
    Route::get ('/',                          'ContratoController@listar');
    Route::get ('/gerar-num',                 'ContratoController@gerar_num');
    Route::post('/deletar',                   'ContratoController@deletar');
    Route::get ('/listar-pessoa/{id_pessoa}', 'ContratoController@listarPorPessoa');
});

Route::group(['prefix'=>'pedido-servicos'], function() {
    Route::get ('/mostrar/{id_pedido_servicos}', 'PedidoServicosController@mostrar');
    Route::get ('/listar-pessoa/{id_pessoa}',    'PedidoServicosController@listarPorPessoa');
    Route::post('/finalizar',                     'PedidoServicosController@finalizar');
    Route::post('/cancelar',                     'PedidoServicosController@cancelar');
});

Route::group(['prefix'=>'orcamento'], function() {
    Route::get ('/',                          'OrcamentoController@listar');
    Route::get ('/mostrar/{id_orcamento}',    'OrcamentoController@mostrar');
    Route::get ('/gerar-num',                 'OrcamentoController@gerar_num');
    Route::post('/salvar',                    'OrcamentoController@salvar');
    Route::post('/mudar-status',              'OrcamentoController@mudar_status');
    Route::post('/deletar',                   'OrcamentoController@deletar');
    Route::get ('/imprimir/{id}',             'OrcamentoController@imprimir');
    Route::post('/conversao-plano',           'OrcamentoController@conversao_plano');
    Route::get ('/listar-pessoa/{id_pessoa}', 'OrcamentoController@listarPorPessoa');
});

Route::group(['prefix'=>'medicamento'], function() {
    Route::get ('/',             'MedicamentoController@listar');
    Route::post('/salvar',       'MedicamentoController@salvar');
    Route::post('/deletar',      'MedicamentoController@deletar');
    Route::get ('/mostrar/{id}', 'MedicamentoController@mostrar');
    Route::get ('/listar',       'MedicamentoController@listar_json');
});

Route::group(['prefix'=>'atestado'], function() {
    Route::get ('/listar/{id_paciente}', 'AtestadoController@listar');
    Route::post('/salvar',               'AtestadoController@salvar');
    Route::post('/deletar',              'AtestadoController@deletar');
    Route::get ('/imprimir/{id}',        'AtestadoController@imprimir');
});

Route::group(['prefix'=>'receita'], function() {
    Route::get ('/listar',                   'ReceitaController@listar');
    Route::post('/salvar',                   'ReceitaController@salvar');
    Route::post('/deletar',                  'ReceitaController@deletar');
    Route::get ('/imprimir/{id}',            'ReceitaController@imprimir');
    Route::get ('/listar-pessoa/{id_pessoa}','ReceitaController@listarPorPessoa');
});

Route::group(['prefix'=>'documento-modelo'], function() {
    Route::get ('/',              'DocumentoModeloController@listar');
    Route::get ('/listar',        'DocumentoModeloController@listar_json');
    Route::get ('/mostrar/{id}',  'DocumentoModeloController@mostrar');
    Route::post('/salvar',        'DocumentoModeloController@salvar');
    Route::post('/deletar',       'DocumentoModeloController@deletar');
    Route::get ('/imprimir/{id}', 'DocumentoModeloController@imprimir');
});

Route::group(['prefix'=>'documento'], function() {
    Route::get ('/listar',                            'DocumentoController@listar');
    Route::get ('/mostrar/{id}',                      'DocumentoController@mostrar');
    Route::post('/salvar',                            'DocumentoController@salvar');
    Route::post('/deletar',                           'DocumentoController@deletar');
    Route::get ('/imprimir/{id}',                     'DocumentoController@imprimir');
    Route::get ('/listar-pessoa/{id_pessoa}/{pasta}', 'DocumentoController@listarPorPessoa');
});

Route::group(['prefix'=>'anamnese'], function() {
    Route::get ('/',                         'AnamneseController@listar');
    Route::get ('/listar',                   'AnamneseController@listar_json');
    Route::get ('/criar',                    'AnamneseController@criar');
    Route::get ('/exibir/{id}',              'AnamneseController@exibir_para_editar');
    Route::get ('/editar/{id}',              'AnamneseController@editar');
    Route::get ('/listar-opcoes/{id}',       'AnamneseController@listar_opcoes');
    Route::post('/salvar',                   'AnamneseController@salvar');
    Route::get ('/mostrar/{id}',             'AnamneseController@mostrar_anamnese');
    Route::get ('/visualizar-anamnese/{id}', 'AnamneseController@visualizar_anamnese');
    Route::get ('/mostrar-resposta/{id}',    'AnamneseController@mostrar_resposta');
    Route::post('/responder-anamnese',       'AnamneseController@responder_anamnese');
    Route::post('/desativar',                'AnamneseController@desativar');
    Route::post('/ativar',                   'AnamneseController@ativar');
});
Route::group(['prefix'=>'IEC'], function() {
    Route::get ('/',                                  'IECController@listar');
    Route::get ('/criar',                             'IECController@criar');
    Route::get ('/exibir/{id}',                       'IECController@exibir_para_editar');
    Route::get ('/editar/{id}',                       'IECController@editar');
    Route::post('/salvar',                            'IECController@salvar');
    Route::get ('/listar-pessoa/{id_pessoa}/{inativos}',         'IECController@listarPorPessoa');
    Route::get ('/listar-pessoa-grafico/{id_pessoa}', 'IECController@listarPorPessoaGrafico');
    Route::get ('/mostrar-resposta/{id}',             'IECController@mostrar_resposta');
    Route::get ('/visualizar-resposta/{id}',          'IECController@visualizar_resposta');
    Route::get ('/favoritar/{id}',                    'IECController@favoritar');
    Route::get ('/ativar/{id}',                       'IECController@ativar');
    Route::get ('/excluir/{id}',                      'IECController@excluir');
    Route::get ('/listar',                            'IECController@listar_json');
    Route::get ('/mostrar/{id}',                      'IECController@mostrar_iec');
    Route::get ('/listar-areas-recomendadas/{id}', 'IECController@listar_areas_recomendadas');
    Route::get ('/historico/{id}',                    'IECController@historico_iec');
    Route::get ('/carregar/{id}',                     'IECController@carregar');
    Route::post('/responder-iec',                     'IECController@responder_iec');
    Route::post('/deletar',                           'IECController@deletar');
    Route::get('/grafico-hist/{json}',                'IECController@histIEC');
    Route::post('/laudo',                             'LaudoController@createLaudo');
    Route::get('/imprimir-laudo/{id}',                'LaudoController@imprimir');
    Route::get('/getDataGrafico/{id}',                'LaudoController@getDataGrafico');
    Route::post('/deletar-laudo',                     'LaudoController@deletar');
});
Route::group(['prefix'=>'anamnese-pessoa'], function() {
    Route::post('/deletar',                   'AnamnesePessoaController@deletar');
    Route::get ('/imprimir/{id}',             'AnamnesePessoaController@imprimir');
    Route::get ('/listar-pessoa/{id_pessoa}', 'AnamnesePessoaController@listarPorPessoa');
});

Route::group(['prefix'=>'etiqueta'], function() {
    Route::get ('/',             'EtiquetaController@listar');
    Route::get ('/listar',       'EtiquetaController@listar_json');
    Route::get ('/mostrar/{id}', 'EtiquetaController@mostrar');
    Route::post('/salvar',       'EtiquetaController@salvar');
    Route::post('/deletar',      'EtiquetaController@deletar');
});

Route::group(['prefix'=>'agenda-status'], function() {
    Route::get ('/',             'AgendaStatusController@listar');
    Route::get ('/listar',       'AgendaStatusController@listar_json');
    Route::get ('/mostrar/{id}', 'AgendaStatusController@mostrar');
    Route::post('/salvar',       'AgendaStatusController@salvar');
    Route::post('/deletar',      'AgendaStatusController@deletar');
});

Route::group(['prefix'=>'agenda-confirmacao'], function() {
    Route::get ('/',             'AgendaConfirmacaoController@listar');
    Route::get ('/listar',       'AgendaConfirmacaoController@listar_json');
    Route::get ('/mostrar/{id}', 'AgendaConfirmacaoController@mostrar');
    Route::post('/salvar',       'AgendaConfirmacaoController@salvar');
    Route::post('/deletar',      'AgendaConfirmacaoController@deletar');
});

Route::group(['prefix'=>'tipo-procedimento'], function() {
    Route::get ('/',             'TipoProcedimentoController@listar');
    Route::get ('/listar',       'TipoProcedimentoController@listar_json');
    Route::get ('/mostrar/{id}', 'TipoProcedimentoController@mostrar');
    Route::post('/salvar',       'TipoProcedimentoController@salvar');
    Route::post('/deletar',      'TipoProcedimentoController@deletar');
});

Route::group(['prefix'=>'historico-agenda'], function() {
    Route::post('/salvar', 'HistoricoAgendaController@salvar');
    Route::get ('/listar', 'HistoricoAgendaController@listar');
});
Route::group(['prefix'=>'downloadPDF'], function() {
    Route::get ('/anamnese/{id}', 'PEF@downAnamnese');
});
Route::group(['prefix'=>'cockpit'], function() {
    Route::get ('/',                                  'CockpitController@listar');
    Route::get ('/filtrar-data/{data}',               'CockpitController@filtrar_data');
    Route::get ('/mostrar/{value}/{data}',            'CockpitController@mostrar');
    Route::get ('/exibir-finalizacao/{value}/{data}', 'CockpitController@exibir_finalizacao');
    Route::get ('grafico1/{data}',                    'CockpitController@grafico1');
    Route::get ('grafico2/{data}',                    'CockpitController@grafico2');
    Route::get ('alcance',                            'CockpitController@alcance');
});
Route::group(['prefix'=>'notificacao'], function() {
    Route::get ('/listar',                      'NotificacaoController@listar');
    Route::post ('/salvar',                     'NotificacaoController@salvar');
    Route::post ('/excluir',                    'NotificacaoController@excluir');
    Route::get ('/listar-por-pessoa/{id}',      'NotificacaoController@listarPorPessoa');
    Route::get ('/visualizar-notificacao/{id}', 'NotificacaoController@visualizar_notificacao');
});

Route::group(['prefix'=>'regras'], function() {
    Route::get ('/',                              'PessoaController@exibir_regras' );
    Route::post('/salvar-regra-associados',       'PessoaController@salvar_regra_associados');
    Route::get ('/exibir-regra-associados/{id}',  'PessoaController@exibir_regras_associados');
    Route::get ('/excluir-regra-associados/{id}', 'PessoaController@excluir_regras_associados');
});
Route::group(['prefix'=>'bordero'], function() {
    Route::get ('/{filtro}',                                                                    'BorderoController@index');
    Route::get ('/atualizar-modalidades',                                          'BorderoController@atualizar_modalidades');
    Route::get ('/imprimir/{id_emp}/{id_membro}/{id_contrato}/{dt_inicial}/{dt_final}', 'BorderoController@imprimir');
    Route::get('/gerar-xls/{id_emp}/{id_membro}/{id_contrato}/{dt_inicial}/{dt_final}', 'BorderoController@gerar_xls');
});
Route::group(['prefix'=>'contratos-por-periodo'], function() {
    Route::get ('/',                                                                 'ContratosPorPeriodoController@index');
    Route::get ('/imprimir/{id_emp}/{id_membro}/{dt_inicial}/{dt_final}/{filtro}/{orientacao}/{exibirF}',   'ContratosPorPeriodoController@imprimir');
});
Route::group(['prefix'=>'resumo-contratos-vendas'], function() {
    Route::get ('/',                                                                       'ResumoContratosVendasController@index');
    Route::get ('/imprimir/{id_emp}/{agrupamento}/{dt_inicial}/{dt_final}/{modalidade}/{exibirF}',   'ResumoContratosVendasController@imprimir');
});

Route::group(['prefix'=>'checkout'], function() {
    Route::get ('/',                                                      'CheckoutController@index');
    Route::get ('/imprimir/{id_emp}/{id_membro}/{dt_inicial}/{dt_final}', 'CheckoutController@imprimir');
});

Route::group(['prefix'=>'transferencia-empresas'], function() {
    Route::get ('/',                                                                 'TransferenciaEmpresasController@index');
    Route::get ('/imprimir/{dt_inicial}/{dt_final}/{orientacao}',   'TransferenciaEmpresasController@imprimir');
});
Route::group(['prefix'=>'teste'], function() {
    Route::get ('/', 'TesteController@testarsaida');
});

Route::group(['prefix'=>'associados-por-periodo'], function() {
    Route::get ('/',                                                                 'AssociadosPorPeriodoController@index');
    Route::get ('/imprimir/{id_emp}/{dt_inicial}/{dt_final}/{filtro}/{valor}',   'AssociadosPorPeriodoController@imprimir');
});

Route::group(['prefix'=>'agendamentos-por-periodo'], function() {
    Route::get ('/', 'AgendamentosPorPeriodoController@index');
    Route::get ('/imprimir/{id_emp}{dt_inicial}/{dt_final}/{filtro}/{valor}',
    'AgendamentosPorPeriodoController@imprimir');
});

Route::group(['prefix'=>'relatorio-atividades'], function() {
    Route::get ('/',                                           'RelatorioAtividadesController@index');
    Route::get ('/listar-contratos',                           'RelatorioAtividadesController@listar_contratos');
    Route::get ('/listar-contratos-antigos',                   'RelatorioAtividadesController@listar_contratos_antigos');
    Route::get ('/listar-planos/{id}',                         'RelatorioAtividadesController@listar_planos');
    Route::get ('/listar-planos-antigos/{id}',                 'RelatorioAtividadesController@listar_planos_antigo');
    Route::get ('/imprimir/{antigo}/{id_contrato}/{id_plano}', 'RelatorioAtividadesController@imprimir');
});

Route::group([ 'prefix' =>'caixa'], function() {
    Route::get('/cadastro-caixa',          'CaixaController@index');
    Route::get('/verificar-situacao',      'CaixaController@verificar_situacao');
    Route::get('/teste',                   'CaixaController@teste');
    Route::get('/abrir-modal/{data}/{id}', 'CaixaController@abrir_modal');
    Route::get('/editar/{id}',             'CaixaController@editar_cadastro_caixa');
    Route::get('/abrir-modal-saldo',       'CaixaController@abrir_modal_saldo');
    Route::get('/resumo-fechamento',       'CaixaController@resumo_fechamento');

    Route::get('/extrato-final',    'CaixaController@extrato_final');
    Route::get('/abrir-caixa',                    'CaixaController@abrir');
    Route::get('/atualizar-modal',                'CaixaController@atualizar_modal');
    Route::post('/salvar-valor-caixa',            'CaixaController@salvar_valor_caixa');
    Route::get('/fechar-caixa',                  'CaixaController@fechar_caixa');
    Route::post('/salvar-cadastro-caixa',         'CaixaController@salvar_cadastro');
    Route::post('/bloquear-cadastro-caixa',       'CaixaController@bloquear_cadastro_caixa');
    Route::post('/desbloquear-cadastro-caixa',    'CaixaController@desbloquear_cadastro_caixa');
    Route::post('/excluir-cadastro-caixa',        'CaixaController@excluir_cadastro_caixa');


    Route::get('mostrar-extrato/dinheiro',           'CaixaController@extrato_dinheiro');
    Route::get('mostrar-extrato/cartao',             'CaixaController@extrato_cartao');
    Route::get('mostrar-extrato/transferencia',      'CaixaController@extrato_transferencia');
    Route::get('mostrar-extrato/convenio',           'CaixaController@extrato_convenio');
    Route::get('mostrar-extrato/recebimentos',       'CaixaController@extrato_recebimentos');
    Route::get('mostrar-extrato/vendas',             'CaixaController@extrato_vendas');
    Route::get('mostrar-extrato/sangria-suprimento', 'CaixaController@extrato_sangria_suprimento');

    Route::get('listar-caixas/{id}',                       'CaixaController@listar_caixas');

    Route::get('/teste',    'CaixaController@teste');

});

Route::group(['prefix' => 'financeiro'], function() {
    // Títulos a pagar
    Route::get('/titulos-pagar',                 'TitulosPagarController@index');
    Route::get('/exibir-titulo-pagar/{id}',      'TitulosPagarController@exibir');
    Route::get('/confirmar-recebimento',         'TitulosPagarController@confirmar_recebimento');
    Route::get('/abrir-modal-baixa-pagar/{id}',  'TitulosPagarController@abrir_modal_baixa');
    Route::get('/baixa-documentos-pagar',        'TitulosPagarController@baixa-documentos-pagar');
    Route::get('/salvar-titulo-pagar',           'TitulosPagarController@salvar');
    Route::get('/salvar-baixa-pagar',            'TitulosPagarController@salvar_baixa_pagar');
    Route::get('/titulos-pagar-liquidados',      'TitulosPagarController@titulos_pagar_liquidados');
    Route::get('/titulos-pagar-abertos',         'TitulosPagarController@titulos_pagar_abertos');
    Route::get('/titulos-pagar/pesquisar',       'TitulosPagarController@pesquisar');
    Route::get('/titulos-pagar/visualizar/{id}', 'TitulosPagarController@visualizar');

    Route::get('/titulos-pagar/comissoes',       'TitulosPagarController@comissoes');

    // Títulos a receber
    Route::get('/titulos-receber',                'TitulosReceberController@index');
    Route::get('/exibir-titulo-receber/{id}',     'TitulosReceberController@exibir');
    Route::get('/confirmar-recebimento',          'TitulosReceberController@confirmar_recebimento');
    Route::get('/abrir-modal-baixa-receber/{id}', 'TitulosReceberController@abrir_modal_baixa');
    Route::get('/baixa-documentos-receber',       'TitulosReceberController@baixa-documentos-receber');
    Route::get('/salvar-titulo-receber',          'TitulosReceberController@salvar');
    Route::get('/salvar-baixa-receber',           'TitulosReceberController@salvar_baixa_receber');
    Route::get('/titulos-receber-liquidados',     'TitulosReceberController@titulos_receber_liquidados');
    Route::get('/titulos-receber-abertos',        'TitulosReceberController@titulos_receber_abertos');
    Route::get('/titulos-receber/pesquisar',      'TitulosReceberController@pesquisar');
    Route::get('/titulos-receber/visualizar/{id}', 'TitulosReceberController@visualizar');
    Route::get('/titulos-receber/cockpit/{filtro}/{data}','TitulosReceberController@from_cockpit');
    
    // Aluguéis
    Route::get('/alugueis',                   'AluguelController@index');
    Route::post('/alugueis/sala/gravar',      'AluguelController@gravarSala');
    Route::post('/alugueis/sala/excluir',     'AluguelController@excluirSala');
    Route::post('/alugueis/aluguel/gravar',   'AluguelController@alugar');
    Route::post('/alugueis/aluguel/encerrar', 'AluguelController@encerrar');
    Route::post('/alugueis/aluguel/dtVenc',   'AluguelController@dtVenc');
});

Route::group(['prefix' => 'contas-bancarias'], function() {
    Route::get('/',                         'ContasBancariasController@index');
    Route::get('/salvar',                   'ContasBancariasController@salvar');
    Route::get('/editar/{id}',              'ContasBancariasController@editar');
    Route::post('/excluir',                  'ContasBancariasController@excluir');
});

Route::group(['prefix' => 'plano-de-contas'], function() {
    Route::get('/',              'PlanoDeContasController@index');
    Route::get('/montar-arvore', 'PlanoDeContasController@montar_arvore');
    Route::get('/abrir-modal',   'PlanoDeContasController@abrir_modal');
    Route::get('/salvar',       'PlanoDeContasController@salvar');
    Route::get('/deletar',     'PlanoDeContasController@deletar');
});

Route::get('/cadastro-de-empresa', 'EmpresaController@listarEmpresas');

Route::group(['prefix' => 'empresa'], function() {
    Route::post("/criar", "EmpresaController@criarEmpresa");
    Route::get("/ver", "EmpresaController@verEmpresa");
    Route::get("/editar", "EmpresaController@editarEmpresa");
    Route::get("/deletar", "EmpresaController@deletarEmpresa");
});

















































Route::group(['prefix'=>'agenda-antiga'], function() {
    Route::get ('/agendamento-info/{id}',               'AgendaAntigaController@agendamento_info');
    Route::get ('/agendamento-info/{id}',               'AgendaAntigaController@agendamento_info');
    Route::post('/copiar-agendamento-id',               'AgendaAntigaController@copiar_agendamento_id');
    Route::post('/dividir-horario-por-id',              'AgendaAntigaController@dividir_horario_por_id');
    Route::post('/confirmar-agendamento',               'AgendaAntigaController@confirmar_agendamento');
    Route::post('/confirmar-agendamento-mobile',        'AgendaAntigaController@confirmar_agendamento_mobile');
    Route::post('/salvar',                              'AgendaAntigaController@salvar_pedido');
    Route::post('/cancelar-agendamento',                'AgendaAntigaController@cancelar_agendamento');
    Route::get ('/listar',                              'AgendaAntigaController@listar');
    Route::post('/mudar-status',                         'AgendaAntigaController@mudar_status');
    Route::get ('/editar_agendamento_/{id}',            'AgendaAntigaController@editar_agendamento');
    Route::get ('/listar-modalidades-disponiveis/{id}', 'AgendaAntigaController@listar_modalidades_disponiveis');
    Route::get('/salvar-agendamento-antigo',           'AgendaAntigaController@salvar_agendamento_antigo');
    Route::get ('/deletar-agendamento',                 'AgendaAntigaController@deletar_agendamento');
    Route::get('/faturar/{id}',                              'AgendaAntigaController@faturar');
});

Route::group(['prefix' => 'encaminhamento'], function() {
    Route::get('/listar/{id}',  'EncaminhamentoController@listar');
    Route::get('/mostrar/{id}', 'EncaminhamentoController@mostrar');
    Route::group(['prefix' => 'anexar'], function() {
        Route::post('/pedido', 'EncaminhamentoController@anexar_de_pedido');
        Route::post('/agenda', 'EncaminhamentoController@anexar_de_agenda');
    });
    Route::group(['prefix' => 'especialidade'], function() {
        Route::get('/listar',           'EncaminhamentoController@listarEspecialidade');
        Route::get('/por-encaminhante', 'EncaminhamentoController@espPorEnc');
    });
    Route::group(['prefix' => 'encaminhante'], function() {
        Route::get ('/obter',   'EncaminhamentoController@obterEncaminhante');
        Route::post('/salvar',  'EncaminhamentoController@criarEncaminhante');
        Route::post('/editar',  'EncaminhamentoController@editarEncaminhante');
        Route::post('/excluir', 'EncaminhamentoController@excluirEncaminhante');
    });
    Route::group(['prefix' => 'solicitacao'], function() {
        Route::get ('/listar',  'EncaminhamentoController@listarSolicitacao');
        Route::get ('/mostrar', 'EncaminhamentoController@mostrarSolicitacao');
        Route::post('/gravar',  'EncaminhamentoController@gravarSolicitacao');
        Route::post('/excluir', 'EncaminhamentoController@excluirSolicitacao');
    });
});

// Route::group(['prefix' => 'encaminhamento'], function() {
//     Route::get('/salvar-encaminhamento',            'EncaminhamentoController@salvarEncaminhamento');  
//     Route::get('/listar-tabelaencaminhamento/{id}',        'EncaminhamentoController@listarTabelaEncaminhamento');
//     Route::get('/mostrar-tabelaencaminhamento/{id_encaminhamento}',  'EncaminhamentoController@mostrarTabelaEncaminhamento');
//     Route::get('/mostrar-tabelaencaminhamento2/{id_paciente}',  'EncaminhamentoController@mostrarTabelaEncaminhamentoPorPessoa');
//     Route::get('/salvarsucesso',                      'EncaminhamentoController@salvarSucess');
//     Route::get('/listar-pessoas{id_pessoa}',            'EncaminhamentoController@listarPessoa');
// });

Route::group(['prefix' => 'ZapSign'], function() {
    Route::get('/cadastrar-signatario/{id}',  'ZapSignController@cadastrar_signatario');
    Route::get('/exibir-contrato/{id}',       'ZapSignController@cadastrar_signatario');

    Route::get('/enviar/email',               'ZapSignController@enviar_email');
    Route::post('/enviar/whatsapp',           'ZapSignController@enviar_whatsapp');
    Route::get('/enviar/email2',              'ZapSignController@enviar_email02');
}); 

Route::post('/csv', 'HomeController@csv');