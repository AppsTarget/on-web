<!-- Modal -->
<style type = "text/css">
div.infoEnc {
    display:none
}
</style>
<div class="modal fade" id="criarAgendamentoModal" aria-labelledby="criarAgendamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:620px">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="criarAgendamentoModalLabel">
                    Criar Agendamento
                    <small class="invalid-feedback"></small>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form id="criar-agendamento-form" class="row" action="/saude-beta/agenda/salvar" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input id="id-grade-horario" name="id_grade_horario" type="hidden" required>
                        <input id="id-profissional" name="id_profissional" type="hidden" required>
                        <input id="id" name="id" type="hidden" required>

                        <div class="col-12 form-search">
                            <label for="paciente_nome" class="custom-label-form">Associado *</label>   
                            <input id="paciente_nome"
                                name="paciente_nome"  
                                class="form-control autocomplete" 
                                placeholder="Digitar Nome do Associado..."
                                data-input="#paciente_id"
                                data-table="pessoa" 
                                data-column="nome_fantasia" 
                                data-filter_col="paciente"
                                data-filter="S"
                                type="text" 
                                autocomplete="off"
                                required>
                            <input id="paciente_id" name="paciente_id" type="hidden">
                        </div>
                        
                        <div class="col-12" id='procedimentos'>
                            <label for="tipo_procedimento" class="custom-label-form">Tipo de Agendamento *</label>   
                                <select id="id_tipo_procedimento" class="custom-select" onchange="control_criar_agendamento(()=>{console.log('a')});">
                                    <option value='0'>Selectionar tipo de agendamento...</option>
                                @foreach($tipo_agendamento as $tipo)
                                    <option value="{{ $tipo->id }}">{{$tipo->descr }}</option>
                                @endforeach
                                </select>

                        </div>
                        <div class="col-md-12" id="convenio-descr" style="display: block">
                            <label for="convenio_id" class="custom-label-form">Convênio</label>
                            <select id="convenio_id" name="convenio_id" class="custom-select" onchange="convenio_control_agendamento();">
                                <option value="0">Selectionar Convenio...</option>
                            </select>
                        </div>
                        

                        <div class="col-md-12 form-group" id="contratos" style="display: block;min-width: 50%;">
                            <label for="id_contrato" class="custom-label-form">Contratos</label>
                            <select id="id_contrato" name="id_contrato" class="custom-select">
                                <option value="0">Selecionar contrato...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 form-group" id="planos_por_contrato" style="display: block; min-width: 50%;">
                            <label for="id_plano" class="custom-label-form">Planos</label>
                            <select id="id_plano" name="id_plano" class="custom-select"  onchange="mostrarModalidadesPorPlano($('#criarAgendamentoModal #id_plano').val())">
                                <option value="0">Selecionar Plano...</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group" style="max-width: 100%;min-width: 66%;display:block" id='procedimento_agenda'>
                            <label for="procedimento-nome-agenda" class="custom-label-form">Selecionar Plano *</label>
                            <select id="procedimento_id" name="procedimento_id" class="custom-select" onchange="mostrarModalidadesPorPlano($('#criarAgendamentoModal #procedimento_id').val())">
                                <option value="0">Selecionar Plano...</option>
                            </select>
                        </div>

                        <div class="col-md-12" id="modalidade-descr" style="display: block">
                            <label for="modalidade_id" class="custom-label-form">Modalidade</label>
                            <select id="modalidade_id" name="modalidade_id" class="custom-select">
                                <option value="0">Selecionar modalidade...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="data" class="custom-label-form">Data*</label>
                            <input id="data" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
                        </div>

                        <div class="col-md-6">
                            <label for="hora" class="custom-label-form">Hora*</label>
                            <input id="hora" name="hora" class="form-control timing" autocomplete="off" type="text" placeholder="__:__" required>
                        </div>
                        <div class="col-md-12 form-group infoEnc">
                            <div class="table-header-scroll" style = "border:1px solid #ced4da;border-top-left-radius:5px;border-top-right-radius:5px;display:none">
                                <table style = "border-color:#ced4da">
                                    <thead>
                                        <tr>
                                            <th width="10%" style = "">&nbsp</th>
                                            <th width="90%" style = "border-right:1px solid #ced4da">Encaminhamentos solicitados</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="table-body-scroll custom-scrollbar" style = "border:1px solid #ced4da;display:none;height:auto;border-bottom-left-radius:5px;border-bottom-right-radius:5px">
                                <table id="tab_solicitacoes" class="table table-hover" style = "table-layout: fixed;margin-bottom:0">
                                    <tbody></tbody>
                                </table>
                            </div>
                            <input type = "hidden" id = "agenda_sol" name = "agenda_sol" value = "0" />
                        </div>
                        <div class="col-md-12 form-group infoEnc" style = "margin-bottom:0">
                            <label for="agenda_encaminhante_nome" class="custom-label-form">Encaminhante</label>
                            <input id="agenda_encaminhante_nome"
                                    name="agenda_encaminhante_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digite o encaminhante"
                                    data-input="#agenda_encaminhante_id"
                                    data-table="enc2_encaminhantes"
                                    data-column="nome_fantasia"
                                    type="text"
                                    autocomplete="off"
                            >
                            <input id="agenda_encaminhante_id" name="agenda_encaminhante_id" type="hidden" onchange = "muda_legenda_encaminhante(this.value)" class = "limpaSol">
                            <span id = "enc_label" class = "custom-label-form" style = "
                                text-align: right;
                                width: 100%;
                                display: block;
                            ">
                                Encaminhante não cadastrado? Clique
                                <a href = "javascript:encaminhanteModal();">aqui</a>
                                para cadastrar
                            </span>
                        </div>

                        <div class="col-md-6 form-group infoEnc">
                            <label for="agenda_enc_esp" class="custom-label-form">Para</label>
                            <select id="agenda_enc_esp" name="agenda_enc_esp" class="custom-select limpaSol"></select>
                        </div>
                        <div class="col-md-6 form-group infoEnc">
                            <button class="btn btn-primary" onclick="$('#enc_arquivo').trigger('click')" style = "margin-top:25px;width:100% !important" type = "button" id = "enc_arquivo-btn">Anexar</button>
                        </div>
                        <div class="col-md-12 form-group" id = "infEncBox" style = 'display:none'>
                            <button class="btn btn-primary" onclick="infEnc()" style = "margin-top:25px;width:100% !important" type = "button">Informações adicionais</button>
                        </div>
                        <div class="col-md-12 form-group infoEnc">
                            <label for="enc_cid_nome" class="custom-label-form">CID</label>
                            <input id="enc_cid_nome" 
                                    name="enc_cid_nome" 
                                    class="form-control autocomplete" 
                                    placeholder="Digitar CID..." 
                                    data-input="#enc_cid_id" 
                                    data-table="cid" 
                                    data-column="nome"  
                                    data-filter="S" 
                                    type="text" 
                                    autocomplete="off" 
                                    onchange=""
                            >
                            <input id="enc_cid_id" name="enc_cid_id" type = "hidden" class = "limpaSol">
                        </div>
                        
                        <div class="col-md-12 mb-5">
                            <label for="obs" class="custom-label-form">Observações</label>
                            <textarea id="obs" name="obs" class="form-control" rows="2"></textarea>
                        </div>

                        <div id="agendamento-footer" class="row col-12 pr-0">
                            <div id='bordero'class="col-md-9 custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;top: -40px;display: none;'>
                                <input id="bordero_b" name="bordero_b" style="width: 17px;margin: -1.5% 5px 0px -6%;" type="checkbox">
                                    <span style='color: #001284;position: relative;'>Não somar agendamento ao Borderô do membro</span>
                            </div>
                            <div class="col-md-12 pr-0 text-center">
                                <button type="submit" form="criar-agendamento-form" class="btn btn-target px-5" id="enviar" value="">Salvar</button>
                                <button type="button" onclick='criarConsultaModal($("#criarAgendamentoModal #id").val())'class="btn btn-target px-5" id="confirmar" value="">Confirmar</button>
                            </div>
                        </div>
                    </form>
                    <form method = "POST" enctype = "multipart/form-data" style = "display:none" id = "enc_arquivo_form">
                        {{ csrf_field() }}
                        <input type="file" name="enc_arquivo" id = "enc_arquivo" />
                        <input type="text" name="enc_agendamento" id = "enc_agendamento" />
                        <input type="text" name="id_paciente" id = "enc_paciente" />
                        <input type="text" name="enc_profissional" id = "enc_profissional" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include("modals/encaminhante_modal")
@include("modals/infsol_modal")