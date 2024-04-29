<!-- Modal -->
<div class="modal fade" id="mudarTipoConfirmacaoModal" aria-labelledby="mudarTipoConfirmacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="mudarTipoConfirmacaoModalLabel">Ação</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12 form-group">
                            <input id="id_agendamento_confirmacao" type="hidden" value="">

                            <label for="tipo_confirmacao" class="custom-label-form">Contato</label>
                            <select id="tipo_confirmacao" name="tipo_confirmacao" class="custom-select">
                                @foreach ($agenda_confirm as $confirm)
                                <option value="{{ $confirm->id }}">{{ $confirm->descr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-target px-5" id="btn-mudar-tipo-confirmacao">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>