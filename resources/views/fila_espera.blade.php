@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

@php
    function mostrar_tempo_espera($chegada) {
        $chegada = strtotime($chegada);
        $agora = strtotime(date('H:i:s'));

        if ($agora > $chegada) {
            $tempo = round((($agora - $chegada) / 60), 0);
            if ($tempo > 60) {
                $tempo  = round($tempo / 60, 0);
                if ($tempo > 1) return '≈ (' . $tempo . ' horas)';
                else            return '≈ (' . $tempo . ' hora)';
            } else {
                if ($tempo > 1) return '≈ (' . $tempo . ' minutos)';
                else            return '≈ (' . $tempo . ' minuto)';
            }
        } else {
            return '';
        }
    }
@endphp


<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Fila de Espera</h3>
    </div>
    <div class="row m-0">
        <div class="col-6 pl-0">
            <label for="selecao-profissional" class=" ml-1 mb-1">
                <small style="color:#667; font-size:70%">SELECIONE UM PROFISSIONAL</small>
            </label>
        </div>
    </div>
    <div class="row pb-2 m-0">
        <div class="col-6 pl-0">
            <div id="selecao-profissional">
                @foreach ($profissionais as $profissional)
                    <a href="/saude-beta/fila-espera/{{ $profissional->id }}">
                        @if (getEmpresaObj()->mod_mostrar_foto && file_exists(public_path('img') . '/pessoa/' . getEmpresa() . '/' . $profissional->id . '.jpg'))
                            @if ($profissional->id == $pessoa->id)
                                <img class="user-photo selected" title="{{ $profissional->nome_fantasia }}"
                            @else
                                <img class="user-photo" title="{{ $profissional->nome_fantasia }}"
                            @endif
                                data-id_profissional="{{ $profissional->id }}"
                                data-nome_profissional="{{ $profissional->nome_fantasia }}"
                                data-nome_reduzido="{{ $profissional->nome_reduzido }}"
                                src="/saude-beta/img/pessoa/{{ $profissional->id }}.jpg">
                        @else
                            <div class="prontuario-user-pic"
                                data-id_profissional="{{ $profissional->id }}"
                                data-nome_profissional="{{ $profissional->nome_fantasia }}"
                                data-nome_reduzido="{{ $profissional->nome_reduzido }}">
                                @php
                                    $aNome = explode(" ", $profissional->nome_fantasia);
                                    echo substr($aNome[0], 0, 1) . substr($aNome[count($aNome) - 1], 0, 1);
                                @endphp
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
           
    {{--
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-fila-espera">
            <input type="text" class="form-control form-control-lg" placeholder="Procurar por..." aria-label="Procurar por..." aria-describedby="btn-filtro">
            <div class="input-group-append">
                <button class="btn btn-secondary btn-search-grid" type="button" id="btn-filtro">
                    <i class="my-icon fas fa-search"></i>
                </button>
            </div>
         </div>
    
    --}}
    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-fila-espera">
                        <th width="10%">Hora Marc.</th>
                        <th width="10%">Hora Cheg.</th>
                        <th width="10%">Espera</th>
                        <th width="25%">Nome da pessoa</th>
                        <th width="10%">Idade</th>
                        <th width="15%">procedimento</th>
                        <th width="15%">Convênio</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-fila-espera" class="table table-hover">
                <tbody>
                    @foreach ($fila_espera as $fila)
                        <tr>
                            <td width="10%">
                                {{ substr($fila->hora, 0, 5) }}
                            </td>
                            <td width="10%">
                                @if (date('d/m/Y', strtotime($fila->data_chegada)) == '31/12/1969')
                                    {{ substr($fila->hora, 0, 5) }}
                                @else
                                    {{ date('H:m', strtotime($fila->data_chegada)) }}
                                @endif
                            </td>
                            <td width="10%">
                                @php
                                    $datetime1 = strtotime($fila->data_chegada);
                                    $datetime2 = strtotime(date('Y-m-d H:i:s'));
                                    $interval  = abs($datetime2 - $datetime1);
                                    $minutes   = round($interval / 60);
                                    echo $minutes . ' minuto';
                                    if (!in_array($minutes, [0, 1])) echo 's';
                                @endphp
                            </td>

                            <td width="25%" onclick="window.location.href = '/saude-beta/pessoa/prontuario/{{ $fila->id_paciente }}'">
                                <div class="d-flex">
                                    <img class="user-photo-sm mr-2" src="/saude-beta/img/pessoa/{{ $fila->id_paciente }}.jpg"
                                        onerror="this.onerror=null;this.src='/saude-beta/img/paciente_default.png'">
                                    <span class="my-auto">{{ strtoupper($fila->nome_paciente) }}</span>
                                </div>
                            </td>
                            <td width="10%">
                                {{ $fila->idade }}
                            </td>
                            <td width="15%">
                                {{ $fila->descr_tipo_procedimento }}
                            </td>
                            <td width="15%">
                                {{ $fila->descr_convenio }}
                            </td>
                            <td width="5%" class="text-center btn-table-action">
                                <i class="my-icon far fa-check-circle" onclick="confirmar_fila_espera({{ $fila->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
