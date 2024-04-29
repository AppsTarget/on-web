@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Tipos de Contato</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-agenda_confirm">
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
                    <tr class="sortable-columns" for="#table-agenda_confirm">
                        <th width="10%" class="text-right">Código</th>
                        <th width="80%">Descrição</th>
                        <th width="10%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-agenda_confirm" class="table table-hover">
                <tbody>
                    @foreach ($agenda_confirm as $confirm)
                        <tr>
                            <td width="10%" class="text-right">{{ $confirm->id }}</td>
                            <td width="80%">{{ $confirm->descr }}</td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit"      onclick="editar_agenda_confirmacao({{ $confirm->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_agenda_confirmacao({{ $confirm->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#agendaConfirmModal">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.agenda_confirmacao_modal')

@endsection
