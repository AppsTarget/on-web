@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Modelos de Documento</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-doc-modelo">
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
                    <tr class="sortable-columns" for="#table-doc-modelo">
                        <th width="10%" class="text-center">Código</th>
                        <th width="50%">Descrição</th>
                        <th width="15%">Ativo</th>
                        <th width="10%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-doc-modelo" class="table table-hover">
                <tbody>
                    @foreach ($doc_modelos as $doc_modelo)
                        <tr>
                            <td width="10%" class="text-center">{{ $doc_modelo->id }}</td>
                            <td width="50%">{{ $doc_modelo->titulo }}</td>
                            <td width="15%">
                                @if($doc_modelo->ativo)
                                    Ativo
                                @else
                                    Inativo
                                @endif
                            </td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit"      onclick="editar_doc_modelo({{ $doc_modelo->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_doc_modelo({{ $doc_modelo->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarDocModeloModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@if ((
        App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'R'   &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'P'   &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'A'
    ) || Auth::user()->id_profissional == 28480002313 || Auth::user()->id_profissional == 443000000)
)
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif
@include('modals.documento_modelo_modal')

@endsection
