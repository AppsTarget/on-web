<!-- Modal -->
<div class="modal fade" id="agendamentosPendentesModal" aria-labelledby="agendamentosPendentesModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="agendamentosPendentesModal">Agendamentos Pendentes</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-6">
                        <label for="membro" class="custom-label-form">Membro</label>
                        <select id="membro" class="custom-select">
                            
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                        <input id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                    </div>
                    <div class="col-3">
                        <label for="data-final" class="custom-label-form">Data Final*</label>
                        <input id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                    </div>
                </div>
                <div class="row" style="margin-bottom: 25px;">
                    <div class="col-4">
                        <label for="somente-finalizados" class="custom-label-form">Status</label>
                        <select id="somente-finalizados" class="custom-select">
                            <option value='A'>Agendados/Reagendados</option>
                            <option value='F'>Finalizados</option>
                            <option value='C'>Cancelados</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="incompletos" class="custom-label-form">Completo/Incompleto</label>
                        <select id="incompletos" class="custom-select">
                            <option value="A">Todos</option>
                            <option value='C'>Completos</option>
                            <option value='I'>Incompletos</option>
                        </select>
                    </div>
                    <div class="col-4" style="text-align:right;padding-top: 25px;">
                        <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" style = "width:100%" type="button" onclick="pesquisarAgendamentosPendentes()">Confirmar</button>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div id='conteudo-lote-agenda' style="display: flex;justify-content: center;flex-wrap: wrap;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="container" id='btn-imprimir-agendamentos' style='display: none'>
                <div class="row my-3">
                    <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="imprimirAgendamento()">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>