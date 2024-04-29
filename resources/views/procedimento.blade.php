@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Modalidades</h3>

        <div class="col-md-3">
            <select id="filtro-especialidade" class="custom-select">
                <option value="0">Selecionar área da saúde...</option>
                @foreach ($especialidades as $especialidade)
                    <option value="{{ $especialidade->id }}">
                        {{ $especialidade->descr }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="filtro-grid-procedimento" class="input-group col-12 mb-3" data-table="#table-procedimentos">
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
                    <tr class="sortable-columns" for="#table-procedimentos">
                        <th width="10%" class="text-right">Código</th>
                        <th width="40%">Descrição</th>
                        <th width="20%">Resumido</th>
                        <th width="20%">Área da saúde</th>
                        <th width="10%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-procedimentos" class="table table-hover">
                <tbody>
                    @foreach ($procedimentos as $procedimento)
                        <tr data-id_especialidade="{{ $procedimento->id_especialidade }}">
                            <td width="10%" class="text-right">
                                {{ str_pad($procedimento->cod_tuss, 8, "0", STR_PAD_LEFT) }}
                            </td>
                            <td width="40%">{{ $procedimento->descr }}</td>
                            <td width="20%">{{ $procedimento->descr_resumida }}</td>
                            <td width="20%">{{ $procedimento->descr_especialidade }}</td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-dollar-sign" style="width: 0.7em;" onclick="adicionar_metas({{ $procedimento->id }})"></i>
                                <i class="my-icon far fa-edit" onclick="editar_procedimento({{ $procedimento->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_procedimento({{ $procedimento->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#procedimentoModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

@include('modals.procedimento_modal')
@include('modals.add_metas')

@endsection
