@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <input id="id-tabela-preco" name="id_tabela_preco" type="hidden">
        <h3 class="col header-color mb-3">Planos</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-tabela-precos">
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
            <i class="my-icon far fa-dollar-sign" style="width: 30px;height: 31px;position: absolute;right: 29px;top: 15px;cursor: pointer;" onclick="abrir_desconto_modal()"></i>
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-tabela-precos">
                        <th width="10%" class="text-right">Código</th>
                        <th width="23%">Descrição</th>
                        <th width="7%">Nº de pessoas</th>
                        <th width="15%" class='text-right'>Máx. atv. p/ semana</th>
                        <th width="10%" class='text-right'>Vigência</th>
                        <th width="10%" class='text-right'>Valor</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="15%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-tabela-precos" class="table table-hover">
                <tbody>
                    @foreach ($tabela_precos as $tabela_preco)
                        <tr>
                            <td width="10%" class="text-right">{{ $tabela_preco->id }}</td>
                            <td width="23%">{{ $tabela_preco->descr }}</td>
                            <td width="7%" class="text-center">{{ $tabela_preco->n_pessoas }}</td>
                            <td width="15%" class="text-right">{{ $tabela_preco->max_atv_semana }} atividades</td>
                            <td width="10%" class=' text-right'>
                                @if ($tabela_preco->vigencia == 30)
                                    Mensal 
                                @elseif ($tabela_preco->vigencia == 60)
                                    Bimestral
                                @elseif ($tabela_preco->vigencia == 90)
                                    Trimestral
                                @elseif ($tabela_preco->vigencia == 180)
                                    Semestral
                                @else
                                    Anual
                                @endif
                                
                            </td>
                            <td width="10%" class=' text-right'>R$ {{ $tabela_preco->valor }}</td>
                            <td width="10%" class="text-center">
                                @if ($tabela_preco->status == 'A')
                                    Ativo
                                @else
                                    Inativo
                                @endif
                            </td>
                            <td width="15%" class="text-center btn-table-action">
                                <i class="my-icon far fa-calendar" style="width: 1em;" onclick="abrirModalVigenciaPlano({{ $tabela_preco->id }})"></i>
                                <i class="my-icon far fa-edit" onclick="editar_tabela_precos({{ $tabela_preco->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_tabela_precos({{ $tabela_preco->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="abrir_tabela_precos()">
    <i class="my-icon fas fa-plus"></i>
</button>
@if ((
        App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S'
    ) || Auth::user()->id_profissional == 28480002313 || Auth::user()->id_profissional == 443000000)
)
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif
@include('modals.tabela_precos_modal2')
@include('modals.tabela_precos_modal')
@include('modals.precos_modal')
@include('modals.desconto_geral_modal')
@include('modals.add_vigencias_modal')

@endsection
