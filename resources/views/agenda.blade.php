@extends('layouts.app')

@section('content')
@include('.components.main-toolbar')

<div class="row h-100 m-0">
    <div class="col-3 h-100 p-3" style="background:#FFF; border-right:solid #dfdfdf 1px;">
        <div class="d-flex">
            <h5 class="header-color">Membro</h5>
            <div class="custom-control custom-switch ml-auto">
                <input id="semanal-diaria" class="custom-control-input" type="checkbox">
                <label for="semanal-diaria" class="custom-control-label">Diária</label>
            </div>
        </div>
        <ul class="selecao-pessoa card">
            @foreach ($profissionais as $profissional)
            <li data-id="{{ $profissional->id }}">
                <i class="my-icon fas fa-user-circle px-3"></i>
                <span>{{ $profissional->nome_fantasia }}</span>
            </li>
            @endforeach
        </ul>
        <hr>
        <div class="d-flex"> 
            <h5 class="header-color">Data da Agenda</h5>
            <div class="btn-group dropright ml-auto">
                <i class="my-icon fas fa-ellipsis-v" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div id="semana-filtro" class="dropdown-menu py-2 px-4">
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-domingo" class="custom-control-input" type="checkbox" data-filtro="1" checked>
                        <label for="semana-filtro-domingo" class="custom-control-label">Domingo</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-segunda" class="custom-control-input" type="checkbox" data-filtro="2" checked>
                        <label for="semana-filtro-segunda" class="custom-control-label">Segunda</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-terca" class="custom-control-input" type="checkbox" data-filtro="3" checked>
                        <label for="semana-filtro-terca" class="custom-control-label">Terça</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-quarta" class="custom-control-input" type="checkbox" data-filtro="4" checked>
                        <label for="semana-filtro-quarta" class="custom-control-label">Quarta</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-quinta" class="custom-control-input" type="checkbox" data-filtro="5" checked>
                        <label for="semana-filtro-quinta" class="custom-control-label">Quinta</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-sexta" class="custom-control-input" type="checkbox" data-filtro="6" checked>
                        <label for="semana-filtro-sexta" class="custom-control-label">Sexta</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input id="semana-filtro-sabado" class="custom-control-input" type="checkbox" data-filtro="7" checked>
                        <label for="semana-filtro-sabado" class="custom-control-label">Sábado</label>
                    </div>
                </div>
            </div>              
        </div>
        <div class="mini-calendar card"></div>
    </div>
    
    <div id="agenda-diaria" class="col h-100 custom-scrollbar" style="background:#f2f2f2; overflow-y:auto;">
        <div class="agendamentos-dia card p-2 m-2">
            <h5 class="text-center">Selecione profissional e uma data para visualizar a agenda.</h5>
        </div>
    </div>

    <div id="agenda-semanal" class="col h-100 custom-scrollbar" style="background:#f2f2f2; overflow-y:auto;">
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="1">
            <h5 class="text-center">
                Domingo
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="2">
            <h5 class="text-center">
                Segunda
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="3">
            <h5 class="text-center">
                Terça
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="4">
            <h5 class="text-center">
                Quarta
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="5">
            <h5 class="text-center">
                Quinta
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="6">
            <h5 class="text-center">
                Sexta
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
        <div class="agendamentos-dia card p-2 m-2" data-dia_semana="7">
            <h5 class="text-center">
                Sábado
            </h5>
            <h6 class="text-center mt-3">
                Não há horários registrados para essa data e profissional.
            </h6>
        </div>
    </div>

    @if (getEmpresaObj()->mod_fila_espera)
        <div class="col-3 h-100 p-3 custom-scrollbar" style="background:#fff; border-left:solid #dfdfdf 1px; overflow-y:auto;">
            <h4 class="text-center header-color">
                Fila de Espera
            </h4>
            <div class="fila-espera" style="height:calc(100% - 40px); overflow-y:auto;">
            </div>
        </div>
    @endif
</div>

<ul id="fila-espera-context-menu" style="display:none">
    <li class="positive" data-function="atender_fila"><i class="material-icons">done</i><span>&nbsp;Atender</span></li>
    <li class="negative" data-function="desistir_fila"><i class="material-icons">clear</i><span>&nbsp;Desistência</span></li>
</ul>

@include('.modals.criar_agendamento_modal')
@include('.modals.cancelar_agendamento_modal')

@endsection