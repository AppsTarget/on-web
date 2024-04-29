<!-- Modal -->
<div class="modal fade" id="criarAgendamentoAntigoModal" aria-labelledby="criarAgendamentoAntigoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:620px">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="criarAgendamentoAntigoModalLabel">
                    Criar Agendamento
                    <small class="invalid-feedback"></small>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form id="criar-agendamento-antigo-form" class="row" action="/saude-beta/agenda-antiga/salvar-agendamento-antigo" method="POST">
                        @csrf
                        <input id="id-grade-horario" name="id_grade_horario" type="hidden">
                        <input id="id-profissional" name="id_profissional" type="hidden">
                        <input id="id" name="id" type="hidden">

                        <div class="col-12 form-search">
                            <label for="paciente_nome" class="custom-label-form">Associado *</label>   
                            <input id="paciente_nome"
                                name="paciente_nome"  
                                class="form-control autocomplete" 
                                placeholder="Digitar Nome do associado..."
                                data-input="#criarAgendamentoAntigoModal #paciente_id"
                                data-table="pessoa" 
                                data-column="nome_fantasia" 
                                data-filter_col="paciente"
                                data-filter="S"
                                type="text" 
                                autocomplete="off">
                            <input id="paciente_id" name="paciente_id" type="hidden">
                        </div>
                        
                        <line-space-color></line-space-color>
                        
                        <div class="col-12" id='procedimentos'>
                            <label for="tipo_procedimento" class="custom-label-form">Tipo de Agendamento *</label>   
                                <select id="id_tipo_procedimento" class="custom-select" onchange="control_criar_agendamento();">
                                    <option value='0'>Selectionar tipo de agendamento...</option>
                                @foreach($tipo_agendamento as $tipo)
                                    <option value="{{ $tipo->id }}">{{$tipo->descr }}</option>
                                @endforeach
                                </select>

                        </div>

                        <div class="col-md-12" id="modalidade-descr" style="display: block">
                            <label for="modalidade_id" class="custom-label-form">Modalidade</label>
                            <select id="modalidade_id" name="modalidade_id" class="custom-select">
                                <option value="0">Selecionar modalidade...</option>
                            </select>
                        </div>
                        <div class="col-md-6" id='agenda-status'>
                            <label for="id-agenda-status" class="custom-label-form">Status</label>
                            <select id="id-agenda-status" name="id_agenda_status" class="custom-select">
                                @foreach ($agenda_status as $status)
                                <option value="{{ $status->id }}">{{ $status->descr }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <line-space-color></line-space-color>

                        <div class="col-md-6">
                            <label for="data" class="custom-label-form">Data*</label>
                            <input id="data" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                        </div>

                        <div class="col-md-6">
                            <label for="hora" class="custom-label-form">Hora*</label>
                            <input id="hora" name="hora" class="form-control timing" autocomplete="off" type="text" placeholder="__:__">
                        </div>

                        <line-space-color></line-space-color>
                        
                        <div class="col-md-12 mb-5">
                            <label for="obs" class="custom-label-form">Observações</label>
                            <textarea id="obs" name="obs" class="form-control" rows="2"></textarea>
                        </div>

                        <div id="agendamento-footer" class="row col-12 pr-0">
                            <div id='bordero'class="col-md-9 custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;top: -40px;display: none;'>
                                <input id="bordero_b" style="width: 17px;margin: -1.5% 5px 0px -6%;" name="bordero_b" type="checkbox">
                                <span style='color: #001284;position: relative;'>Não somar agendamento ao Borderô do membro</span>
                            </div>
                            <div class="col-md-12 pr-0 text-center">
                                <button type="submit" form="criar-agendamento-antigo-form" class="btn btn-target px-5" id="enviar">Salvar</button>
                                <button type="button" onclick='criarConsultaAntigoModal($("#criarAgendamentoAntigoModal #id").val())'class="btn btn-target px-5" id="confirmar" value="">Confirmar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>