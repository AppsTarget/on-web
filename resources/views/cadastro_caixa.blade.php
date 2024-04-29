@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Cadastro de Caixa</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-forma-pag">
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
                    <tr class="sortable-columns" for="#table-forma-pag">
                        <th width="10%" class="text-center">Código</th>
                        <th width="27%">Descrição</th>
                        <th width="25%">Empresa</th>
                        <th width="15%" class="text-center">Abertura</th>
                        <th width="15%" class="text-center">Fechamento</th>
                        <th width="8%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-forma-pag" class="table table-hover">
                <tbody>
                    @foreach($caixas As $caixa)
                        <tr>
                            <td width="10%" class="text-center">{{$caixa->id}}</td>
                            <td width="27%">{{ $caixa->descr }}
                            <td width="25%">{{ $caixa->empresa }}</td>
                            <td width="15%" class="text-center">{{ $caixa->h_abertura }}h</td>
                            <td width="15%" class="text-center">{{ $caixa->h_fechamento }}h</td>
                            <td width="8%">
                            @if ($caixa->ativo == 'S')
                                <img onclick="bloquearCadastroCaixa({{$caixa->id}})"  style="cursor:pointer; width: 18px;opacity:.7; margin-top: -2px" src="http://vps.targetclient.com.br/saude-beta/img/bloqueado.png">
                            @else 
                                <img onclick="desbloquearCadastroCaixa({{$caixa->id}})" style="cursor:pointer; width: 18px;opacity:.7" src="http://vps.targetclient.com.br/saude-beta/img/desbloquear.png">
                            @endif
                            <i style="margin: 1px 10px 1px 10px;cursor:pointer" class="my-icon far fa-edit"      onclick="editarCadastroCaixa({{ $caixa->id }})"></i>
                            <i style="cursor:pointer" class="my-icon far fa-trash-alt" onclick="excluirCadastroCaixa({{ $caixa->id }})"></i>
                            </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#cadastroCaixaModal">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.cadastro_caixa_modal')

@endsection
