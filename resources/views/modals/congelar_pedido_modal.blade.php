<!-- Modal -->
<div class="modal fade" id="congelarPedidoModal" aria-labelledby="congelarPedidoModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <input type="hidden" id='id_pedido'>
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="congelarPedidoModal">Congelar Contrato</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12" style="padding-right: 70px;padding-left: 70px;">
                                <label for="data" class="custom-label-form">Digite a data de reativação do contrato</label>
                                <input id="data_congelar" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="id" name="id" class="btn btn-target m-auto px-5" type="button" onclick="congelarContrato()">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>