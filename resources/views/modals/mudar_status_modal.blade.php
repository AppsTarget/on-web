<!-- Modal -->
<div class="modal fade" id="mudarStatusModal" aria-labelledby="mudarStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="mudarStatusModalLabel">Mudar Status do Agendamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12 form-group">
                            <input id="id_agendamento_status" type="hidden" value="">

                            <label for="status_agendamento" class="custom-label-form">Status</label>
                            <select id="status_agendamento" name="status_agendamento" class="custom-select">
                                @foreach ($agenda_status as $status)
                                <option value="{{ $status->id }}">{{ $status->descr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-target px-5" id="btn-mudar-status">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>