@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Status da Agenda</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-agenda_status">
            <input type="text" class="form-control form-control-lg" placeholder="Procurar por..." aria-label="Procurar por..." aria-describedby="btn-filtro">
            <div class="input-group-append">
                <button class="btn btn-secondary btn-search-grid" type="button" id="btn-filtro">
                    <i class="my-icon fas fa-search"></i>
                </button>
            </div>
         </div>
    </div> 
    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-agenda_status">
                        <th width="10%" class="text-right">Código</th>
                        <th width="20%">Descrição</th>
                        <th width="10%" style="white-space: pre-wrap !important;">Permite    Editar</th>
                        <th width="10%">Permite Reagendar</th>
                        <th width="10%">Padrao Cancelado</th>
                        <th width="10%">Padrão Reagendado</th>
                        <th width="10%">Padrão Confirmado</th>
                        <th width="5%">Fundo</th>
                        <th width="5%">Texto</th>
                        <th width="10%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-agenda_status" class="table table-hover">
                <tbody>
                    @foreach ($agenda_status as $status)
                        <tr>
                            <td width="10%" class="text-right">{{ $status->id }}</td>
                            <td width="20%">{{ $status->descr }}</td>
                            <td width="10%">
                                @if ($status->permite_editar)
                                    Sim
                                @else
                                    Não
                                @endif
                            </td>
                            <td width="10%">
                                @if ($status->permite_reagendar)
                                Sim
                                @else
                                Não
                                @endif
                            </td>
                            <td width="10%">
                                @if ($status->caso_cancelar)
                                    Sim
                                @else
                                    Não
                                @endif
                            </td>
                            <td width="10%">
                                @if ($status->caso_reagendar)
                                    Sim
                                @else
                                    Não
                                @endif
                            </td>
                            <td width="10%">
                                @if ($status->caso_confirmar)
                                    Sim
                                @else
                                    Não
                                @endif
                            </td>
                            <td width="5%">
                                <div class="list-item-color" data-color="{{ $status->cor }}"
                                    style="background-color: {{ $status->cor }}; border-color: {{ $status->cor }}"></div>
                            </td>
                            <td width="5%">
                                <div class="list-item-color" data-color="{{ $status->cor_letra }}"
                                    style="background-color: {{ $status->cor_letra }}; border-color: {{ $status->cor_letra }}"></div>
                            </td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit"      onclick="editar_agenda_status({{ $status->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_agenda_status({{ $status->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#agendaStatusModal">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.agenda_status_modal')

@endsection
