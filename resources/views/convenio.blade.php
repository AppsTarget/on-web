@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Convênios</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-convenios">
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
                    <tr  class="sortable-columns" for="#table-convenios">
                        <th width="10%" class="text-right">Código</th>
                        @if (getEmpresaObj()->mod_planos_tratamento || getEmpresaObj()->mod_financeiro)
                            <th width="40%">Descrição</th>
                            <th width="20%">Planos</th>
                            <th width="20%">Quem Paga</th>
                        @else
                            <th width="80%">Descrição</th>
                        @endif
                        <th width="10%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-convenios" class="table table-hover">
                <tbody>
                    @foreach ($convenios as $convenio)
                        <tr>
                            <td width="10%" class="text-right">{{ $convenio->id }}</td>
                            @if (getEmpresaObj()->mod_planos_tratamento || getEmpresaObj()->mod_financeiro)
                                <td width="40%">{{ $convenio->descr }}</td>
                                <td width="20%">
                                    @if ($convenio->tabela_preco_descr != '')
                                        {{ $convenio->tabela_preco_descr }}
                                    @else
                                        ——————
                                    @endif
                                </td>
                                <td width="20%">
                                    @if ($convenio->cliente_nome != '')
                                        {{ $convenio->cliente_nome }}
                                    @else
                                        ——————
                                    @endif
                                </td>
                            @else
                                <td width="80%">{{ $convenio->descr }}</td>
                            @endif
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit" onclick="editar_convenio({{ $convenio->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_convenio({{ $convenio->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#convenioModal2">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.convenio_modal')
@include('modals.convenio_moda2l')

@endsection
