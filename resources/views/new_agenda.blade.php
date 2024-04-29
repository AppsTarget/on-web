    @extends('layouts.app')

    @section('content')
    @include('.components.main-toolbar')

    <ul id="agenda-context-menu">
        <li data-function="novo_agendamento">
            <i class="my-icon far fa-calendar-plus"></i>
            <span>Novo Agendamento</span>
        </li>
        <li data-function="novo_agendamento_antigo">
            <i class="my-icon far fa-calendar-plus"></i>
            <span>Novo Agendamento (sistema_antigo)</span>
        </li>
        {{-- <li data-function="colar_agendamento">
            <i class="my-icon far fa-paste"></i>
            <span>Colar</span>
        </li> --}}
        <li data-function="bloquear_desbloquear_grade">
            <i class="my-icon far fa-power-off"></i>
            <span>Bloquear/Desbloquear grade</span>
        </li>
    </ul>

    <ul id="agendamento-context-menu">
        <li data-function="cancelar-agendamento">
            <i class="my-icon far fa-ban"></i>
            <span>Cancelar agendamento</span> 
        </li>
        <li data-function="editar_agendamento">
            <i class="my-icon fas fa-pen-square"></i>
            <span>Editar</span>
        </li>
        <li data-function="mudar_status">
            <i class="my-icon fas fa-exchange"></i>
            <span>Mudar Status</span>
        </li>
        {{-- <li data-function="confirmado_via">
            <i class="my-icon fas fa-id-card"></i>
            <span>Definir Contato</span>
        </li> --}}
        {{-- <li data-function="copiar_agendamento">
            <i class="my-icon far fa-copy"></i>
            <span>Copiar</span>
        </li> --}}
        <li data-function="colar_agendamento">
            <i class="my-icon far fa-paste"></i>
            <span>Colar</span>
        </li>
        <li data-function="repetir_agendamento">
            <i class="my-icon fas fa-redo-alt"></i>
            <span>Reagendar</span>
        </li>
        <li data-function="abrir_prontuario">
            <img style="width: 24px;margin: 0px 2px 0px -4px;" src="{{ asset('img/historico-medico.png')}}">
            <span>Abrir prontuário</span>
        </li>
        <li data-function="bloquear_desbloquear_grade">
            <i class="my-icon far fa-power-off"></i>
            <span>Bloquear/Desbloquear grade</span>
        </li>
        <li data-function="deletar_agendamento">
            <i class="my-icon far fa-trash-alt"></i>
            <span>Deletar</span>
        </li>
        {{-- <li data-function="ver_historico_agenda">
            <i class="my-icon far fa-history"></i>
            <span>Ver Histórico do Status</span>
        </li>
        <li data-function="ver_historico_confirmacao">
            <i class="my-icon far fa-history"></i>
            <span>Ver Histórico de Contato</span>
        </li> --}}
        <li data-function="confirmar_agendamento">
            <i class="my-icon far fa-check"></i>
            <span>Finalizar atendimento</span>
        </li>
        <li data-function="adicionar_agendamento">
            <i class="my-icon far fa-plus"></i>
            <span>Adicionar agendamento</span>
        </li>
        <li data-function="adicionar_agendamento_antigo">
            <i class="my-icon far fa-calendar-plus"></i>
            <span>Adicionar agendamento (sistema_antigo)</span>
        </li>
    </ul>

    <div style='position:relative; top:-30px' class="container-fluid h-100 py-4 px-5" style="color:#212529">
        <div class="mb-2">
            <div class="row m-0">
                <div class="col-6 pl-0">
                    <label for="selecao-profissional" class=" ml-1 mb-1">
                        <small style="color:#667; font-size:70%" onclick="$('#agendamentoLoteModal').modal('show');'"></small>
                    </label>
                </div>
                <div class="col-6 pr-0 text-right">
                    <label for="selecao-profissional" class="ml-1 mb-1" style="position: relative;top: 15px;font-size: 20px;">
                        <small id="dia-selecionado" class="my-auto" style="color:#667">--/--/----</small>
                    </label>
                </div>
            </div>
            <div class="row pb-2 m-0">
                <div class="col-6 pl-0">
                    <div id="selecao-profissional" style='z-index:0;'>
                        @for ($i=0; $i < sizeof($profissionais); $i++)
                        @if (getEmpresaObj()->mod_mostrar_foto && file_exists(public_path('img') . '/pessoa/' . getEmpresa() . '/' . $profissionais[$i]->id . '.jpg'))
                            {{-- <h4 style="@if($i!=0)display:none; @endif " class="nome-profissional-mobile">
                                {{  ucfirst($profissionais[$i]->nome_fantasia)  }}
                            </h4> --}}
                                <div @if($i!=0) style="display:none" @endif class='user-photo-sm agenda-livre-exi{{$agendamentos_ar[$i]}}' title="{{ $profissionais[$i]->nome_fantasia }}"
                                    data-id_profissional="{{ $profissionais[$i]->id }}"
                                    data-nome_profissional="{{ $profissionais[$i]->nome_fantasia }}"
                                    data-nome_reduzido="{{ $profissionais[$i]->nome_reduzido }}">
                                    <img style="max-width: 100%;max-height: 100%;min-width: 100%;min-height: 100%;object-fit: cover;transform: rotate(315deg);border-radius: 100%;"
                                        src="/saude-beta/img/pessoa/{{ getEmpresa() }}/{{ $profissionais[$i]->id }}.jpg">
                                </div>
                            @else
                                <h4 style="@if($i!=0)display:none; @endif " class="nome-profissional-mobile">
                                    {{  ucfirst($profissionais[$i]->nome_fantasia)  }}
                                </h4>
                                <div style="@if($i!=0)display:none; @endif border-radius:100%;margin-right: 15px;"
                                    class="agenda-livre-exi{{$agendamentos_ar[$i]}}"
                                    data-id_profissional="{{ $profissionais[$i]->id }}"
                                    data-nome_profissional="{{ $profissionais[$i]->nome_fantasia }}"
                                    data-nome_reduzido="{{ $profissionais[$i]->nome_reduzido }}">
                                    <div  class="prontuario-user-pic" style="max-width: 100%;max-height: 100%;min-height: 100%;min-width: 100%;transform: rotate(315deg);">
                                        @php
                                            $aNome = explode(" ", $profissionais[$i]->nome_fantasia);
                                            echo substr($aNome[0], 0, 1) . substr($aNome[count($aNome) - 1], 0, 1);
                                        @endphp
                                    </div>
                                </div>
                            @endif
                        @endfor
                        {{-- <div onclick="controle_agenda_semanal()" id='nome-membro-agenda-semanal' class="row m-0" style="background:#f7f7f7margin-left: 10px !important;margin-top: 5px !important;">
                            <div class="col d-grid pl-0" style='cursor: pointer;'>
                                <h4 id="profissional-selecionado" class="header-color-2 my-auto" >Selecionar Membro</h4>
                            </div>
                        </div> --}}
                        @if(sizeof($profissionais) > 1)
                            <input id="agenda_profissional"
                                style="margin-top: 3px;margin-bottom: -1px;color: rgb(8, 129, 255);font-size: 1.5rem;font-weight: 500;line-height: 1.2;font-family: Arial, sans-serif;filter: brightness(0.9);padding: 0px 0px 0px 70px;z-index: 0;position: absolute;left: -6px;"
                                name="agenda_profissional"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome do Profissional..."
                                data-input="#agenda_profissional_id"
                                data-table="pessoa"
                                data-column="nome_fantasia"
                                data-filter_col="colaborador"
                                data-filter="P"
                                type="text"
                                autocomplete="off"
                                required
                                onclick="controlFormAgenda()">
                            <input id="agenda_profissional_id" name="agenda_profissional_id" type="hidden">
                        @endif
                    </div>
                </div>
                <div class="col-6 agenda-options">

                    {{-- <span style="width:350px"></span> <input id="buscar-agendamento" class="form-control bg-white" autocomplete="off" placeholder="Pesquisar Agendamento..." type="text" style="width:250px" required>
                    <input id="paciente_nome"
                    name="paciente_nome"
                    class="form-control autocomplete"
                    placeholder="Digitar Nome do associado..."
                    data-input="#paciente_id"
                    data-table="pessoa"
                    data-column="nome_fantasia"
                    data-filter_col="paciente"
                    data-filter="S"
                    type="text"
                    autocomplete="off"
                    required>
                    --}}
                    <div class="hide-agenda" style="width:calc(100% - 325px)">
                        @if(sizeof($profissionais) > 1)
                            <div style='position: absolute;left: 130px;top: 14px;height: 42px !important;border: 1px solid white;width: 42px !important;'>
                                <button type="button" onclick="abrir_modal_recepcao(true);" style="border: 1px solid #dfdfdf;
                                border-radius: 2px;
                                background: white;
                                width: 100%;"><svg class="svg-inline--fa fa-calendar-alt fa-w-14 my-icon" aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg></button>
                            </div>
                        @endif
                    <button  id="btn-buscar-agendamento" name="btn-buscar-agendamento" title="Alterar Data" type="button" class="btn btn-white custom-card" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <p><i class="my-icon far fa-search"></i> Pesquisar</p>
                    </button>
                    </div>
                    <div class="dropdown dropleft">
                        <button title="Alterar Data" type="button" class="btn btn-white custom-card" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="my-icon far fa-calendar-alt"></i>
                        </button>
                        <div id="filtro-semana" class="dropdown-menu p-0" aria-labelledby="dropdownMenuButton">
                            <div class="mini-calendar card"></div>
                        </div>
                    </div>

                    <div id="mudar-visualizacao" class="btn-group custom-card" role="group" aria-label="Mudar Visualização">
                        <button type="button" class="btn btn-white" data-visualizacao="#agenda-diaria" title="Agenda Diária">
                            <i class="my-icon fas fa-calendar-day"></i>
                        </button>
                        {{-- <button type="button" class="btn btn-white" data-visualizacao="#agenda-diaria" title="Agenda Diária">
                            <i class="my-icon fas fa-columns"></i>
                        </button> --}}
                        <button type="button" class="btn btn-white active" data-visualizacao="#agenda-semanal" title="Agenda Semanal">
                            <i class="my-icon fas fa-calendar-week"></i>
                        </button>
                    </div>

                    <div id="mudar-data-visualizacao" class="btn-group custom-card" role="group" aria-label="Mudar Data">
                        <button type="button" class="btn btn-white" data-function="-" title="Retroceder">
                            <i class="my-icon fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-white" data-function="today" title="Hoje">
                            <p>Hoje</p>
                        </button>
                        <button type="button" class="btn btn-white" data-function="+" title="Avançar">
                            <i class="my-icon fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id='help-colors-agenda-semanal' class="help-colors" style="position: absolute;right: 4%;">
            <i class="my-icon far fa-question-circle" onclick="controle_agenda_semanal()"></i>
            <div class="card">
                @foreach ($agenda_status as $status)
                    <div>
                        <i class="my-icon fad fa-square" style="--fa-primary-color: {{ $status->cor }}"></i>
                        <span>{{ $status->descr }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="agenda-visualizacao">
            @include('components.agenda-diaria')
            @include('components.agenda-semanal')
        </div>
        <script>
            window.addEventListener('load', () => {
                if (detectar_mobile()){
                    setTimeout(() => {
                        document.querySelector("#mudar-visualizacao >button").click()
                        if($('#agenda-diaria').length) {
                            $('.btn-criar-agendamento').each(function() {
                                if ($(this).parent().find('li').length == 0) {
                                    $(this).find('div').attr('style', 'font-weight: 100 !important')
                                }
                            })
                        }
                    }, 500)
                }
            })
        </script>
        <div>
            <button class="btn btn-primary custom-fab" style="bottom:69px" type="button" id="agenda-pessoa">
                <a href="http://vps.targetclient.com.br/saude-beta/paciente">
                    <img style="max-width: 100%;filter: invert(1);" src="http://vps.targetclient.com.br/saude-beta/img/user.png">
                </a>
            </button>
            @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
             App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A'
            )
            <button class="btn btn-primary custom-fab" type="button" onclick="criarModalAgendamentoLote()">
                <a href="#">
                    <img style="max-width: 85%;filter: invert(1);scale:1.6" src="http://vps.targetclient.com.br/saude-beta/img/lote.png">
                </a>
            </button>
            @endif
        </div>
        {{-- @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
            <button class="btn btn-primary custom-fab" onclick="novo_agendamento();" type="button" data-toggle="modal" data-target="#criarAgendamentoModal">
                <i class="my-icon fas fa-plus"></i>
            </button>
        @endif --}}
    </div>
    <input type = "hidden" id = "estaNaAgenda" value = "estaNaAgenda" />
    <script type = "text/javascript" language = "JavaScript">
        window.addEventListener("load", function() {
            document.getElementById("agenda-pessoa").style.display = screen.height > screen.width ? "" : "none";
            
            setInterval(() => {
                mostrar_agendamentos_semanal()
            }, 5000)
        });
    </script>

    @include('.modals.grade_bloqueio_modal')
    @include('.modals.pessoa_modal')
    @include('.modals.criar_agendamento_modal')
    @include('.modals.agendamento_em_lote_modal')
    @include('.modals.criar_agendamento_antigo_modal')
    @include('.modals.pedido_modal2')
    @include('.modals.pedido_antigo_modal')
    @include('.modals.cancelar_agendamento_modal')
    @include('.modals.cancelar_agendamento_antigo_modal')
    @include('.modals.agenda_pesquisa_modal')
    @include('.modals.historico_agenda_modal')
    @include('.modals.mudar_status_modal')
    @include('.modals.mudar_tipo_confirmacao_modal')
    @include('.modals.adicionar_fila_espera_modal')
    @include('.modals.agendamento_lote_modal')
    @include('.modals.agendamento_lote_modal2')
    @include('.modals.view_agenda_recepcao_modal')
    @include('.modals.ajuste_modal')
    @include('.modals.agenda_mobile_modal')
    @include('.modals.confirmar_agendamento_modal')
    @include('modals.encaminhamentosLista_modal')
    @include('.modals.listar_tabelasEncaminhamento_modal')
    @include('.modals.visualizar_agendamentos_em_lote')
    @include("modals.listar_tabelasEncaminhamento_modal")
    {{-- @include('.modals.pedido_consulta_modal') --}}


    @endsection
