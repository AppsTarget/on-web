<!-- Modal -->
<div class="modal fade" id="finalizarPedidoServicosModal" aria-labelledby="finalizarPedidoServicosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="finalizarPedidoServicosModalLabel">
                    Finalizar procedimento
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="form-finalizar-pedido-servicos" class="row" action="/saude-beta/pedido-servicos/finalizar" method="POST">
                        @csrf
                        <input id="id_pedido_servicos" name="id_pedido_servicos" type="hidden">

                        <div class="col-12 form-group form-search">
                            <label for="descr_profissional" class="custom-label-form">
                                Profissional
                            </label>
                            <input id="descr_profissional"
                                name="descr_profissional"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome do Profissional..."
                                data-input="#id_profissional"
                                data-table="pessoa"
                                data-column="nome_fantasia"
                                data-filter_col="colaborador"
                                data-filter="P"
                                type="text"
                                autocomplete="off"
                                required>
                            <input id="id_profissional" name="id_profissional" type="hidden">
                        </div>

                        <div class="col-6 mt-2">
                            <label for="data" class="custom-label-form">Data</label>
                            <input id="data" name="data" class="form-control date" autocomplete="off" type="text" value="{{ date('d/m/Y') }}" required>
                        </div>

                        <div class="col-6 mt-2">
                            <label for="hora" class="custom-label-form">Hora</label>
                            <input id="hora" name="hora" class="form-control timing" autocomplete="off" type="text" value="{{ date('H:i') }}" required>
                        </div>

                        <div class="col-12 d-grid">
                            <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
