<!-- Modal -->
<div class="modal fade" id="inserirQtdConvModal" aria-labelledby="inserirQtdConvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="inserirQtdConvModal">Inserir Quantidade</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <input id="id_plano" name="id_plano" type="hidden">
                    
                    <div class="col-md-12">
                        <label for="qtd" class="custom-label-form">Quantidade *</label>
                        <input id="qtd" name="qtd" class="form-control" autocomplete="off" type="number" step="1">
                    </div>
                    <div class='d-flex' style="justify-content: center">
                        <button onclick='inserir_qtde_conversao();' type="button" class="btn btn-target my-3 mx-auto px-5">
                            Gravar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>