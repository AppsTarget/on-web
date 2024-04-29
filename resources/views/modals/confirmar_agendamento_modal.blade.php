
<!-- Modal -->
<div class="modal fade" id="ConfirmacaoModal" aria-labelledby="ConfirmacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="agendaConfirmModalLabel">Tipo de Confirmação</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/saude-beta/agenda-confirmacao/salvar" method="POST">
                <input type="hidden" id="id_agendamento">
                <input type="hidden" id="antigo">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3 form-group" id="contatos" style="min-width: 50%;">
                                <label for="id_contato" class="custom-label-form">Contratos</label>
                                <select id="id_contato" name="id_contato" class="custom-select" style=''>
                                    <option value="0">Selecionar tipo de contato...</option>
                                    <option value="1">Confirmar Presença</option>
                                    <option value='2'>Associado Ausente</option>
                                    <option value="3">Finalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex">
                    <button type="button" onclick="salvar_confirmacao($('#agendaMobileModal #antigo').val())" class="btn btn-target mx-auto my-3 px-5">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>