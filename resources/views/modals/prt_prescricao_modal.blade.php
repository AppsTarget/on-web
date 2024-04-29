<!-- Modal -->
<div class="modal fade" id="criarPrescricaoModal" aria-labelledby="criarPrescricaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarPrescricaoModalLabel">Gerar Prescrição</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="form-prescricao" class="row" action="/saude-beta/prescricao/salvar" method="POST" onsubmit="salvar_prescricao(event)">
                        @csrf
                        <div class="col-3 form-group">
                            <label for="data" class="custom-label-form">Data da Prescrição</label>
                            <input id="data" name="data" class="form-control date" autocomplete="off" type="text" value="{{ date('d/m/Y') }}" required>
                        </div>
                        <div class="col-12 form-group">
                            <h5 class="custom-label-form">Nova Prescrição</h5>
                            <textarea id="corpo" name="corpo" class="summernote" required></textarea>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-target my-3 mx-auto px-5">Gerar Prescrição</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
