<div class="main-toolbar">
    @if ((App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S') || Auth::user()->id_profissional == 28480002313 || Auth::user()->id_profissional == 443000000)
        <a onclick="redirecionar('cockpit')">
    @else
        <a onclick="redirecionar('')">
    @endif
        <img style='max-width: 100%;max-height: 100%;' src="/saude-beta/img/logo_topo_limpo_on.png">
    </a>

    <div id="buscar-paciente">
        <input type="text" class="form-control autocomplete" data-input="#id_main_busca_paciente" data-table="pessoa" data-column="nome_fantasia" data-filter_col="paciente" data-filter="S" placeholder="Buscar Associado..." aria-label="Digitar por Associado..." aria-describedby="btn-filtro">
        <div class="input-group-append">
            <button class="btn btn-secondary btn-search-grid" type="button" id="btn-filtro">
                <img src="/saude-beta/img/search.png">
            </button>
        </div>
        <input id="id_main_busca_paciente" type="hidden">
    </div>

    <div class="btn-toolbar px-3 mr-auto">
        @if (!Auth::user()->sa)
        <a href="/saude-beta" title="Agenda">
            <i class="my-icon fal fa-calendar-alt"></i>
            <span>Agenda</span>
        </a>
        @else
        <a href="/saude-beta/administrador" title="Administrador">
            <i class="my-icon fal fa-user-cog"></i>
            <span>Administrador</span>
        </a>
        @endif
        {{-- @if (getProfissional()->colaborador == 'P' or getProfissional()->colaborador == 'R') --}}
        {{-- <a href="/saude-beta/fila-espera/{{ getProfissional()->id }}" title="Fila de Espera"> --}}
            {{-- <i class="my-icon fal fa-users-class"></i> --}}
            {{-- <span>Fila de Espera</span> --}}
        {{-- </a> --}}
        {{-- @endif --}} {{-- todo_ verificar --}} 

        @if (Auth::user()->id_profissional != 28480002247)

        <a title="Pessoas">
            {{-- <img src="/saude-beta/img/user.png"> --}}
            <i class="my-icon fal fa-user"></i>
            <span>Pessoas</span>
            <img class="dropdown-icon" src="/saude-beta/img/sort-down.png">
            <ul class="dropdown-toolbar">
                <li onclick="redirect('/saude-beta/paciente')" title="Pessoas">
                    <span>Pessoas</span>
                </li>
            </ul>
        </a>

        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A'
            )
             <a title = "Vendas">
                    <img  style="max-width: 32px;
                                position: relative;
                                top: 19px;
                                right: -25px;" src="/saude-beta/img/bolsa-de-compras.png">
                    <span style="position: relative;
                    top: 11px;
                    height: 26px;">Vendas</span>
                    <img class="dropdown-icon" src="/saude-beta/img/sort-down.png">
                    <ul class="dropdown-toolbar">
                        <li onclick="redirect('/saude-beta/pedido')" title="Contratos">
                            <span>Contratos</span>
                        </li>
                        <li onclick="abrirModalCaixa()" title="Caixa">
                            <span>Caixa</span>
                        </li>
                    </ul>
                </a>
        @endif

        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' || 
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R'   ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
             @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' && Auth::user()->id_profissional <> 28480001071)
                <a href="#" title="Financeiro">
                    <i class="my-icon fal fa-usd-circle" title="Financeiro"></i>
                    <span>Financeiro</span>
                    <img class="dropdown-icon" src="/saude-beta/img/sort-down.png">
                    <ul class="dropdown-toolbar">
                        <li title="Títulos A Pagar">
                            <span>Títulos A Pagar <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                            <ul class="subdropdown-toolbar">
                                <li onclick="redirect('/saude-beta/financeiro/titulos-pagar')" title="Baixa de documentos">
                                    Baixa de documentos
                                </li>
                                <li onclick="redirect('/saude-beta/financeiro/titulos-pagar/comissoes')" title="Baixa de comissões">
                                    Baixa de comissões
                                </li>
                                <li onclick="redirect('/saude-beta/bordero/E')" title="Borderô do membro">
                                    Borderô do membro
                                </li>
                            </ul>
                        </li>
                        
                        <li title="Títulos A Receber">
                            <span>Títulos A Receber<img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                            <ul class="subdropdown-toolbar">
                                <li onclick="redirect('/saude-beta/financeiro/alugueis')" title="Aluguéis">
                                    Aluguéis
                                </li>
                                <li onclick="redirect('/saude-beta/financeiro/titulos-receber')" title="Baixa de documentos">
                                    Baixa de documentos
                                </li>
                                <!--
                                <li onclick="redirect('/saude-beta/financeiro/titulos-receber-abertos')" title="Títulos em aberto">
                                    Títulos em aberto
                                </li>
                                <li onclick="redirect('/saude-beta/financeiro/titulos-receber-liquidados')" title="Títulos liquidados">
                                    Títulos liquidados
                                </li>
                                -->
                            </ul>
                        </li>
                        
                    </ul>
                </a>
            @endif
            @if (Auth::user()->id_profissional <> 28480002313 && Auth::user()->id_profissional <> 443000000)
                <a href="#" title="Relatórios">
                    <i class="my-icon fal fa-file-chart-line"></i>
                    <span>Relatórios</span>
                    <img class="dropdown-icon" src="/saude-beta/img/sort-down.png">
                    <ul class="dropdown-toolbar">
                        <li style="padding: 10px 20px 10px 21px;" onclick="redirect('/saude-beta/contratos-por-periodo')" title="Contratos por período">
                            Contratos por período
                        </li>
                        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' || Auth::user()->id_profissional == 28480001071)
                            <li style="padding: 10px 20px 10px 21px;"  onclick="redirect('/saude-beta/bordero/E')" title="Borderô do membro">
                                Borderô do membro
                            </li>
                            <li style="padding: 10px 20px 10px 21px;"  onclick="redirect('/saude-beta/resumo-contratos-vendas')" title="Resumo Contratos/Vendas">
                                Resumo Contratos/Vendas
                            </li>
                        @endif
                        <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="abrirModalAgendamentosPendentes()" title="Agendamentos Pendentes">
                            Agendamentos Pendentes
                        </li>
                        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' || Auth::user()->id_profissional == 28480001071)
                            <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/associados-por-periodo')" title="Relatório de Associados">
                                Associados
                            </li>
                            <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/transferencia-empresas')" title="Transferência Entre Empresas">
                                Transferência Entre Empresas
                            </li>
                            
                            {{-- <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/agendamentos-por-periodo')" title="Agendamentos por Período">
                                Agendamentos Por Período
                            </li>
                            <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/relatorio-de-pessoas')" title="Agendamentos por Período">
                                Pessoas
                            </li> --}}
                        @endif
                        {{-- 
                        <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/relatorio-atividades')" title="Histórico de atividades">
                            Histórico de atividades
                        </li>
                        <li style="padding: 10px 20px 10px 21px;line-height:15px" onclick="redirect('/saude-beta/checkout')" title="Checkout">
                            Checkout
                        </li>
                        --}}
                    </ul>
                </a>
            @endif
        @endif
        {{-- <a href="/saude-beta/parametros" title="Parâmetros"> --}}
            {{-- <img src="/saude-beta/img/reports.png"> --}}
            {{-- <i class="my-icon fal fa-cogs"></i>
            <span>Parâmetros</span>
        </a> --}}
        @if ((App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' || 
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R'   ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A'   || 
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'P') AND
             Auth::user()->id_profissional <> 28480002313 AND Auth::user()->id_profissional <> 28480002247)
            <a id="parametros">
                <i class="my-icon fal fa-cogs" title="Parâmetros" ></i>
                <span>Parâmetros</span>
                <img class="dropdown-icon" src="/saude-beta/img/sort-down.png">
                <ul class="dropdown-toolbar">
                    {{-- <li title="Geral">
                        <span>Geral <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                        <ul class="subdropdown-toolbar">
                            <li onclick="redirect('/saude-beta/especialidade')" title="especialidade">
                                Especialidade
                            </li>
                            <li onclick="redirect('/saude-beta/procedimento')" title="procedimentos">
                                procedimentos
                            </li>
                            <li onclick="redirect('/saude-beta/profissional')" title="Profissionais">
                                Profissionais
                            </li>
                            <li onclick="redirect('/saude-beta/sala')" title="Salas">
                                Salas
                            </li>
                        </ul>
                    </li> --}}
                    @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                        <li title="Agenda">
                            <span>Agenda <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                            <ul class="subdropdown-toolbar">
                                <li onclick="redirect('/saude-beta/etiqueta')" title="Etiquetas">
                                    Etiquetas
                                </li>
                                <li onclick="redirect('/saude-beta/agenda-status')" title="Status da Agenda">
                                    Status da Agenda
                                </li>
                                <li onclick="redirect('/saude-beta/agenda-confirmacao')" title="Tipo de Confirmação">
                                    Tipo de Confirmação
                                </li>
                                {{-- <li onclick="redirect('/saude-beta/tipo-procedimento')" title="Tipos de procedimento">
                                    Tipo de Agendamento
                                </li> --}}
                            </ul>
                        </li>
                    @endif
                    @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador <> 'P' || Auth::user()->id_profissional == 360000000) 
                        <li title="Faturamento">
                            <span>Faturamento <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                            <ul class="subdropdown-toolbar">
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                                    <li onclick="redirect('/saude-beta/especialidade')" title="Áreas da saúde">
                                        <span>Áreas da saúde</span>
                                    </li>
                                    <li onclick="redirect('/saude-beta/procedimento')" title="Modalidades">
                                        <span>Modalidades</span>
                                    </li>
                                @endif
                                    <li onclick="redirect('/saude-beta/profissional/E')" title="Membros">
                                        <span>Membros</span>
                                    </li>
                                    
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                                    <li onclick="redirect('/saude-beta/tabela-precos')" title="Planos">
                                        <span>Planos</span>
                                    </li>
                                    {{-- <li onclick="redirect('/saude-beta/regras')" title="Regras de Associados">
                                        <span>Regras de Associados</span>
                                    </li> --}}
                                    <li onclick="redirect('/saude-beta/caixa/cadastro-caixa')" title="Cadastro de Caixa">
                                        Cadastro de Caixa
                                    </li>
                                @else
                                    {{-- <li onclick="redirect('/saude-beta/profissional')" title="Membros">
                                        <span>Membros</span>
                                    </li> --}}
                                @endif
                            </ul>
                        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                            <li title="Financeiro">
                                <span>Financeiro <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                                <ul class="subdropdown-toolbar">
                                    <li onclick="redirect('/saude-beta/cliente')" title="Clientes">
                                        Clientes
                                    </li>
                                    <li onclick="redirect('/saude-beta/convenio')" title="Convênios">
                                        Convênios
                                    </li>
                                    <li onclick="redirect('/saude-beta/financeira')" title="Financeira">
                                        Financeira
                                    </li>
                                    <li onclick="redirect('/saude-beta/forma-pag')" title="Formas de Pagamento">
                                        Formas de Pagamento
                                    </li>
                                    <li onclick="redirect('/saude-beta/contas-bancarias')" title="Contas Bancárias">
                                        Contas Bancárias
                                    </li>
                                    
                                    <li onclick="redirect('/saude-beta/plano-de-contas')" title="Plano de Contas">
                                        Plano de Contas
                                    </li>

                                    <li onclick="redirect('/saude-beta/cadastro-de-empresa')" title="Cadastro de empresa">
                                        Cadastro de empresa
                                    </li>
                                    {{-- <li onclick="ajustar_modalidades()" title="Formas de Pagamento">
                                        Ajustar Modalidades
                                    </li> --}}
                                </ul>
                            </li>
                        @endif
                    @endif
                    <li title="Prontuário">
                        <span>Prontuário <img class="dropdown-icon" src="/saude-beta/img/sort-down.png"></span>
                        <ul class="subdropdown-toolbar">
                            <li onclick="redirect('/saude-beta/anamnese')" title="Anamnese">
                                Anamnese
                            </li>
                            <li onclick="redirect('/saude-beta/IEC')" title="IEC (cadastros)">
                                IEC (cadastros)
                            </li>
                            <li onclick="redirect('/saude-beta/medicamento')" title="Medicamentos">
                                Medicamentos
                            </li>
                            <li onclick="redirect('/saude-beta/documento-modelo')" title="Modelos de Documento">
                                Modelos de Documento
                            </li>
                            <li onclick="redirect('/saude-beta/evolucao-tipo')" title="Tipo de Evolução">
                                Tipo de Evolução
                            </li>
                        </ul>
                    </li>
                    
                </ul>
            </a>
        @endif
        @endif
    </div>

    <h3 id="empresa-toolbar" class="hide-mobile" style="display: flex;align-items: center;margin-top: 10px;margin-right: 10px;position: absolute;right: 12%;top: 31%;font-size: 70%;">
        {{ getEmpresaObj()->descr }}
    </h3>

    <div class="btn-secondary-toolbar">
        {{-- <a class="ml-2" href="/saude-beta/parametros" title="Parâmetros">
            <img src="/saude-beta/img/config.png">
        </a> --}}
        <a class="ml-2" href="#" id="notificacao" title="Notificações" onclick="listar_notificacoes();">
            <img src="/saude-beta/img/notification.png">
            <span class="qtde-notificacao">0</span>
            
        </a>
        
        {{-- <a class="ml-2" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Sair">
            <img src="/saude-beta/img/logout.png">
        </a> --}}
    </div>
    

    <div class="d-flex mx-3">
        <div class="user-card d-flex my-auto" style="margin-top: 10px !important;margin-bottom: 0px !important">
            @if (file_exists(public_path('img') . '/pessoa/' . getEmpresa() . '/' . Auth::user()->id_profissional . '.jpg'))
                <img id="imagem-usuario" style="cursor:pointer;width: 65px;height:65px;object-fit:cover;border-radius: 100%;border: 4px solid #167af6;margin: 0px 20px 0px 20px;"
                    src="/saude-beta/img/pessoa/{{ Auth::user()->id_profissional }}.jpg">
            @else
                <div style=" border-radius:100%;margin-right: 15px;">
                    <div  class="prontuario-user-pic">
                        @php
                            $aNome = explode(" ", App\Pessoa::find(Auth::user()->id_profissional)->nome_fantasia);
                            echo substr($aNome[0], 0, 1) . substr($aNome[count($aNome) - 1], 0, 1);
                        @endphp
                    </div>
                </div>
            @endif
            <ul class="dropdown-toolbar-user">
                {{-- <li onclick="redirect('/saude-beta/parametros')" title="Parâmetros">
                    Parâmetros
                </li> --}}
                <li onclick="abrirModalAlterarEmpresa()" title="Parâmetros">
                    Trocar Empresa
                </li>
                <li onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Sair">
                    <span class="pb-2">Sair</span>
                </li>
            </ul>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>