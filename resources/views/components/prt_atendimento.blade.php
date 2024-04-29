<div class="container card mb-5 p-3">
    <h5 class="mx-3 mb-3">Atendimentos</h5>
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="accordion w-100 px-3 msg-if-empty" data-empty_msg="Não há atendimentos realizados com esse paciente.">@if(count($atendimentos) != 0)
                    @foreach ($atendimentos as $atendimento)
                        <div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">
                            <div class="accordion-header w-100">
                                <div class="row m-0">
                                    <div class="col"> 
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#agendamento-{{ $atendimento->id }}" aria-expanded="true" aria-controls="collapse1">
                                            {{ date('d/m/Y', strtotime($atendimento->data_inicio)) }}
                                        </button> 
                                    </div>
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#agendamento-{{ $atendimento->id }}" aria-expanded="true" 
                                        aria-controls="collapse1" style="margin-right:15px">
                                        @if ($atendimento->hora_fim != null) 
                                            {{ substr($atendimento->hora_inicio, 0, 5) }} - 
                                            {{ substr($atendimento->hora_fim, 0, 5) }}
                                            @php
                                                $start = strtotime($atendimento->hora_inicio);
                                                $end   = strtotime($atendimento->hora_fim);
                                                $interval  = abs($end - $start);
                                                $mins   = round($interval / 60);
                                                if ($mins > 60) echo ' (' . round($mins / 60) . ' horas) ';
                                                else            echo ' (' . $mins             . ' mins) ';
                                            @endphp 
                                        @else 
                                            Em Atendimento
                                        @endif
                                    </button> 
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif</div>
            </div>
        </div>
    </div>
</div>