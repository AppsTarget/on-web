<!-- Modal -->
<div class="modal fade" id="cancelarAgendamentoAntigoModal" aria-labelledby="cancelarAgendamentoAntigoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="/saude-beta/agenda-antiga/cancelar-agendamento" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="cancelarAgendamentoAntigoModalLabel">Cancelar Agendamento</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="paciente" class="custom-label-form">Associado</label>
                                <input id="paciente" name="paciente" class="form-control" autocomplete="off" type="text"readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="hora" class="custom-label-form">Hora</label>
                                <input id="hora" name="hora" class="form-control" autocomplete="off" type="text" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="data" class="custom-label-form">Data</label>
                                <input id="data" name="data" class="form-control" autocomplete="off" type="text" readonly>
                            </div>

                            <div class="col-md-6 d-grid">
                                <div class="my-auto">
                                    <div class="custom-control custom-radio my-2">
                                        <input id="cliente" name="motivo" class="custom-control-input" value="1" type="radio" checked>
                                        <label for="cliente" class="custom-control-label">Solicitado pelo Cliente</label>
                                    </div>
                                    <div class="custom-control custom-radio my-2">
                                        <input id="profissional" name="motivo" class="custom-control-input" value="2" type="radio">
                                        <label for="profissional" class="custom-control-label">Solicitado pelo Profissional</label>
                                    </div>
                                    <div class="custom-control custom-radio my-2">
                                        <input id="clinica" name="motivo" class="custom-control-input" value="3" type="radio">
                                        <label for="clinica" class="custom-control-label">Solicitado pela Clínica</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="obs" class="custom-label-form">Observações</label>
                                    <textarea id="observacao" name="observacao" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container">
                        <div class="row float-right mt-3">
                            <button id="id" name="id" class="btn btn-primary" type="submit">Cancelar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
