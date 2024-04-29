@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">IEC</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-anamnese">
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
                    <tr class="sortable-columns" for="#table-anamnese">
                        <th width="10%" class="text-right">Código</th>
                        <th width="65   %">Descrição</th>
                        <th width="10%" class='text-center'>Status</th>
                        <th width="10%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-anamnese" class="table table-hover">
                <tbody>
                    @foreach ($IECs as $IEC)
                        <tr>
                            <td width="10%" class="text-right">{{ $IEC->id }}</td>
                            <td width="70%">{{ $IEC->descr }}</td>
                            <td width="10%" class='text-center'>
                                @if ($IEC->ativo == 'S')
                                    <div class="btn btn-sm px-4 tag-fila-espera text-center">Ativo</div>
                                @else
                                    <div class="btn btn-sm px-4 tag-fila-atendimento text-center">Inativo</div>
                                @endif
                            </td>
                            <td width="10%" class="text-center btn-table-action">
                                <svg class="svg-inline--fa fa-edit fa-w-18 my-icon" onclick="redirect('/saude-beta/IEC/editar/{{ $IEC->id }}')" aria-hidden="true" focusable="false" data-prefix="far" data-icon="edit" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"></path></svg>
                                <i class="my-icon far fa-times-square" onclick="excluir_IEC({{ $IEC->id }})"></i>
                                <img src='img/ligar.png' onclick="ativar_IEC({{ $IEC->id }})" id="reativar" width="15px">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="redirect('/saude-beta/IEC/criar')">
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
@endsection
