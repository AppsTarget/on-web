<!-- Modal -->
<div class="modal fade" id="evolucaoTipoModal" aria-labelledby="evolucaoTipoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="evolucaoTipoModalLabel">Cadastrar Tipo de Evolução</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/evolucao-tipo/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">
                        
                        <div class="col-md-12">
                            <label for="descr" class="custom-label-form">Descrição *</label>
                            <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-12">
                            <div class="custom-control custom-checkbox mt-2">
                                <input id="prioritario" name="prioritario" class="custom-control-input" type="checkbox">
                                <label for="prioritario" class="custom-control-label">Prioritário</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-target my-3 mx-auto px-5">
                            Gravar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>