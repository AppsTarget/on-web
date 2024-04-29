@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<link href="{{ asset('css/font-awesome6.css') }}" rel="stylesheet">
<input id="id_pessoa_prontuario" type="hidden" value="{{ $pessoa->id }}">
@php
    if ($pessoa->iec_atrasado == "S") {
        echo "<script>alert('Há IECs em atraso!');</script>";
    }
@endphp
<style>
    /* #app{
    overflow: hidden;
}     */
    body {
        overflow: hidden !important;
    }
</style>
<div class="row h-100 m-0" >
    <div onclick="recolher_menu()"class="menu-trigger" id="menu-trigger" style="position: absolute">
        <img class = "menu-trigger" src="../../img/seta-esquerda.png">
	</div>
    <div class="col-3 h-100 p-3 menu-hidde" id="menu-hidde" style="background:#FFF">
        <div class="header-menu-prontuario h-100">
            <h3 class="hide-mobile">Prontuário</h3>
            @if (getEmpresaObj()->mod_tempo_consulta) 
                <button  id="iniciar_atendimento" class="btn btn-lg btn-block btn-success hide-mobile" type="button" data-id_paciente="{{ $pessoa->id }}" data-em_atendimento="N">
                    <i class="my-icon fas fa-play mr-2"></i>
                    <span>Iniciar Atendimento</span>
                </button>
                @include('modals.pessoa_modal')
                <div class="custom-control custom-checkbox mt-2 hide-mobile">
                    <input id="cbx-video-chamada" class="custom-control-input" type="checkbox">
                    <label for="cbx-video-chamada" class="custom-control-label">Atendimento por Vídeo</label>
                </div>
                <hr>
            @endif  
            <ul id="menu-prontuario" class="custom-scrollbar" @if (!getEmpresaObj()->mod_tempo_consulta) style="height:calc(100% - 60px)" @endif>
                <ul id="menu-prontuario" class="custom-scrollbar"> 
                <li data-id="#prt-resumo" class="selected" onclick="resumo_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-user mr-2"></i>
                    <span>Resumo</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>

                <li data-id="#prt-IEC" class="selected" onclick="iec_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-user mr-2"></i>
                    <span>IEC</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-Anexos" class="selected" onclick="anexos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-folder mr-2"></i>
                    <span>Anexos</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-solicitacoes" class="selected" onclick="solicitacoes_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="fa6-light fa6-file-export" style = "margin-right:6px"></i>
                    <span>Checkouts</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-checkout" class="selected" onclick="encaminhamentos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="fa6-light fa6-memo-circle-check" style = "margin-right:6px"></i>
                    <span>Checkouts realizados</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-Graficos" class="selected" onclick="graficos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-heart mr-2"></i>
                    <span>Saúde</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-Notificacao" class="selected" onclick="notificacao_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-bell mr-2"></i>
                    <span>Histórico de notificações</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>


                
                {{-- <li data-id="#prt-vitruviano" class="selected" onclick="resumo_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-user mr-2"></i>
                    <span>Vitruviano</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-agendamentos" class="selected" onclick="resumo_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-user mr-2"></i>
                    <span>Agendamentos</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li> --}}

                <hr>

                @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' ||
                     App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R'   ||
                     App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                     <li class="hide-mobile"><b>Planos & Propostas</b></li>

                    <li class="hide-mobile" data-id="#prt-plano-tratamento" onclick="pedidos_por_pessoa($('#id_pessoa_prontuario').val())">
                        <i class="my-icon fal fa-money-check mr-2"></i>
                        <span>Contratos</span>
                        <div class="qtde-prontuario" data-count="0">
                            <small class="m-auto">
                                0
                            </small>
                        </div>
                    </li>
                    <li  class="hide-mobile" data-id="#prt-creditos" onclick="creditos_por_pessoa($('#id_pessoa_prontuario').val())">
                        <i class="my-icon fal fa-money-check-alt mr-2"></i>
                        <span>Histórico de créditos</span>
                        <div class="qtde-prontuario" data-count="0">
                            <small class="m-auto">
                                0
                            </small>
                        </div>
                    </li>
                    {{--
                    <li class="hide-mobile" data-id="#prt-atividades" onclick="atividades_por_pessoa()"> 
                        <img  class="icone-atividades" src="/saude-beta/img/atividade.png" alt="ícone de atividades">
                        <span>Atividades</span>
                        <div class="qtde-prontuario" data-count="0">
                            <small class="m-auto">
                                0
                            </small>
                        </div>
                    </li>    
                    --}}
                @endif   
                <li class="esconder" data-id="#prt-orcamento" onclick="orcamentos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-money-check-alt mr-2"></i>
                    <span>Propostas de Tratamento</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>

                <hr>
                <li><b>Tratamento</b></li>
                <li data-id="#prt-evolucao" onclick="evolucoes_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-notes-medical mr-2"></i>
                    <span>Evoluções Avulsas</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li  class="hide-mobile" data-id="#prt-agendamento" onclick="agendamentos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-calendar-alt mr-2"></i>
                    <span>Agendamentos</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-anamnese" onclick="anamneses_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-page-break mr-2"></i>
                    <span>Anamnese</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-documento" onclick="documentos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-file-alt mr-2"></i>
                    <span>Documentos</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-prescricao" onclick="prescricoes_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-file-prescription mr-2"></i>
                    <span>Prescrições</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                <li data-id="#prt-receita" onclick="receitas_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-file-medical mr-2"></i>
                    <span>Receita</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li>
                {{-- <li data-id="#prt-anexos" onclick="anexos_por_pessoa($('#id_pessoa_prontuario').val())">
                    <i class="my-icon fal fa-paperclip mr-2"></i>
                    <span>Imagens e Anexos</span>
                    <div class="qtde-prontuario" data-count="0">
                        <small class="m-auto">
                            0
                        </small>
                    </div>
                </li> --}}
            </ul>
        </div>
    </div>
    <div id="prontuario" class="col-9 h-100 p-3" style="background:#f2f2f2; overflow-y:auto;">

        <div class="container-fluid card mb-3">
            <div class="row pt-3 m-0">
                <div class="col-2 d-grid foto-associado-prontuario" onclick="editar_pessoa({{$pessoa->id}})">
                    <img class="user-photo" src="/saude-beta/img/pessoa/{{ $pessoa->id }}.jpg"
                        onerror="this.onerror=null;this.src='/saude-beta/img/paciente_default.png'"> 
                </div>

                <div class="col d-grid">
                    <div class="my-auto">
                        <h6 class="header-color" style="font-weight:600; text-transform:uppercase">{{ $pessoa->nome_fantasia }}</h6>
                        @if (getEmpresaObj()->mod_cod_interno)
                            <span class="hide-mobile">Código do Associado: <b>{{ $pessoa->cod_interno }}</b></span>
                            <br class="hide-mobile">
                        @endif
                        @php
                            if ($pessoa->data_nasc != null && $pessoa->data_nasc != 'null' && $pessoa->data_nasc != ''){
                                $birthDate = date('d/m/Y', strtotime($pessoa->data_nasc));
                                $birthDateAux = date('d/m/Y', strtotime($pessoa->data_nasc));
                                $birthDate = explode("/", $birthDate);
                                $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                                    ? ((date("Y") - $birthDate[2]) - 1)
                                    : (date("Y") - $birthDate[2]));
                            }
                            else {
                                $age = 0;
                                $birthDateAux = "Não Informado";
                            }
                        @endphp 
                        <span>Nascimento: <b>{{ $birthDateAux }} ({{ $age }} anos)</b></span>
                        <br>
                        <span>Primeira consulta em: <b>{{ $primeira_consulta }}</b></span>
                        <br>
                        <span>Consultas: <b>{{ $pessoa->qtde_consulta }}</b></span> 
                    </div>
                </div>

                <div class="col-4 d-grid offset-1 hide-mobile">
                    <button type="button" class="btn btn-target btn-lg btn-block my-auto"  onclick="enviarEmailRedefinição({{ $pessoa->id }})" style="position: absolute;font-size: 85%;width: 45%;right: 0px;top: 0px;padding: 5px 5px 5px 5px;">
                        Redefinir Senha Por e-mail
                    </button>
                </div>
            </div>

            <div class="row py-3 m-0">
                <div class="col text-center hide-mobile ">
                    <div class="mx-auto d-flex">
                        <i class="my-icon fal fa-envelope my-auto mr-2"></i>
                        <span>Email:</span>
                        <b>&nbsp;{{ $pessoa->email }}</b> 
                    </div>
                </div>
                <div class="col text-center hide-mobile ">
                    <div class="mx-auto d-flex">
                        <i class="my-icon fal fa-phone my-auto mr-2"></i>
                        <span>Telefone:</span>
                        <b>&nbsp;{{ $pessoa->celular1 }}</b> 
                    </div>
                </div>
                <div class="col text-center hide-mobile ">
                    <div class="mx-auto d-flex">
                        <i class="my-icon fal fa-map-marker-alt my-auto mr-2"></i>
                        <span>Local:</span>
                        @if ($pessoa->cidade != '')
                        <b>&nbsp;{{ $pessoa->cidade . '/' . $pessoa->uf }}</b>
                        @else
                        <b>&nbsp;Não cadastrado.</b>
                        @endif 
                    </div>
                </div>
                <div class="d-flex text-center hide-mobile">
                    {{-- <a class="ml-auto" href="#" onclick="editar_pessoa({{ $pessoa->id }})">Mais informações</a> --}}
                    <i class="my-icon fal fa-chevron-right mr-auto my-auto ml-2"></i>
                </div>
            </div>
            {{-- <div id="content">
                @include('.components.prt_carrosselProntuario')
            </div> --}}
        </div>
        <div class="card mb-3"> 
            <hr style="margin-top:0; margin-bottom:0;">
            <div class="box-atividades" style = "display:block;height:auto">
                <table>
                    <tr>
                        <td style = 'width:100%'>
                            <h2 id="total-atividades-prontuario">TOTAL DE ATIVIDADES VÁLIDAS: {{ $total }}</h2>
                            <h2 id="total-atividades-prontuario">DISPONÍVEL PARA AGENDAMENTO: {{ $disponivel }}</h2>
                            <h2 id="total-atividades-prontuario">AGENDADAMENTOS EM ABERTO: {{ $agendados }}</h2>
                        </td>
                        <td>
                            <div style="height:20px;margin-top:-20px" onclick="atv_pessoa({{ $pessoa->id }}, '{{ $pessoa->nome_fantasia }}')">
                                <img id="olho-open-modal" src="/saude-beta/img/olho.png" alt="Olho">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="prt-resumo" class="selected">
            @include('.components.prt_resumo')
        </div>
        <div id="prt-IEC" class>
            @include('.components.prt_iec')
        </div>
        <div id="prt-Anexos" class>
            @include('.components.prt_anexo')
        </div>
        <div id="prt-solicitacoes" class>
            @include('.components.prt_solicitacoes')
        </div>
        <div id="prt-checkout" class>
            @include('.components.prt_checkout')
        </div>
        <div id="prt-Graficos" class>
            @include('.components.prt_graficos')
        </div>
        <div id="prt-Notificacao" class>
            @include('.components.prt_notificacao')
        </div>
        {{-- <div id="prt-vitruviano" class="selected">
            @include('.components.prt_vitruviano')
        </div>
        <div id="prt-agendamentos" class="selected">
            @include('.components.prt_agendamentos_por_pessoa')
        </div> --}}
        {{-- <div id="prt-contratos" class="selected">
            @include('.components.contratos')
        </div>
        <div id="prt-agendamentos">
            @include() --}}




        <div id="prt-evolucao" class='height-100'>               @include('.components.prt_evolucao')   </div>
        <div id="prt-procedimentos-aprovados" class='height-100'>@include('.components.prt_procedimento_aprovado')</div>
        <div id="prt-agendamento" class='height-100'>            @include('.components.prt_agendamento')</div> 
        {{-- <div id="prt-atendimento">            @include('.components.prt_atendimento')     </div> --}}
        <div id="prt-anamnese" class='height-100'>               @include('.components.prt_anamnese')        </div>
        <div id="prt-documento" class='height-100'>              @include('.components.prt_documento')       </div>
        <div id="prt-prescricao" class='height-100'>             @include('.components.prt_prescricao')       </div>
        <div id="prt-receita" class='height-100'>                @include('.components.prt_receita')          </div>
        <div id="prt-anexos" class='height-100'>                 @include('.components.prt_anexo')            </div>
        <div id="prt-orcamento" class='height-100'>              @include('.components.prt_orcamento')        </div>
        <div id="prt-plano-tratamento" class='height-100'>       @include('.components.prt_plano_tratamento') </div>  
        <div id="prt-creditos" class='height-100'>               @include('.components.prt_creditos') </div> 
        <div id="prt-atividades" class='height-100'>             @include('.components.prt_atividades') </div>
    </div>
</div>
<script>
    window.addEventListener('load', () => {
        if (detectar_mobile()){
            recolher_menu()
        }
        else if ($('#lista .item').length > 0) {
            document.querySelector('#lista .item').click()
        }
        iec_por_pessoa($('#id_pessoa_prontuario').val(), true)
    })
</script>
<button class="btn-voltar-agenda-mobile btn btn-primary custom-fab" type="button">
    <a href="http://vps.targetclient.com.br/saude-beta">
        <img style="width: 30px; filter: invert(1)"src="{{ asset('img/agenda.png') }}">
    </a>
</button>

@include('.modals.atv_pessoa_modal')
@include('.modals.atv_contrato_modal')
@include('.modals.log_contrato_modal')
@include('.modals.conversao_credito_modal')
@include('.modals.inserir_qtd_conversao')
@include('modals.video_chamada_modal')
@include('components.video_chamada')
@include('.modals.prt_modal_vitruviano')
@include('.modals.resumo_vitruviano_modal')
@include('.modals.prt_evolucao_modal')
@include('.modals.agendados_atividades_modal')
@include('.modals.lista_agendamentos_diario_modal')
@include('.modals.enviando_email_modal')


@endsection
