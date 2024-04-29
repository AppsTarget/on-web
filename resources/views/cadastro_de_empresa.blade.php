@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Cadastro de empresa</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-empresa">
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
                    <tr class="sortable-columns" for="#table-empresa">
                        <th width="11%" class="text-center">Código</th>
                        <th width="32%">Descrição</th>
                        <th width="18%" class="text-left">Responsável</th>
                        <th width="12%" class="text-center">Telefone responsável</th>
                        <th width="12%" class="text-center">Telefone empresa</th>
                        <th width="5%" class="text-right"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-empresa" class="table table-hover">
                <tbody>
                   @foreach ($empresas as $empresa)
                        <tr>
                            <td width="11%">{{$empresa->codigo}}</td>
                            <td width="32%">{{$empresa->descr}}</td>
                            <td width="18%">{{$empresa->responsavel}}</td>
                            <td width="12%" class = "td_telefone">{{$empresa->tel_responsavel}}</td>
                            <td width="12%" class = "td_telefone">{{$empresa->tel_empresa}}</td>
                            <td width="5%">
                                <i class="my-icon far fa-edit"      onclick="editar_empresa({{ $empresa->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_empresa({{ $empresa->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick = "openModalEmpresa(true);">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.empresa_modal')

@endsection
