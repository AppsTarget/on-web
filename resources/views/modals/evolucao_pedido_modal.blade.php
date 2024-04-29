<!-- Modal -->
<div class="modal fade" id="evolucaoPedidoModal" aria-labelledby="evolucaoPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="evolucaoPedidoModalLabel">Cadastrar Evolução do
                    procedimento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <form id="form-salvar-evolucao-pedido" class="row"
                                action="/saude-beta/evolucao-pedido/salvar" method="POST"
                                onsubmit="salvar_evolucao_pedido(event)">
                                @csrf

                                <input id="id_pedido_servicos" name="id_pedido_servicos" type="hidden">

                                {{-- <div class="col-12 form-group">
                                    <label for="id_evolucao_tipo" class="custom-label-form">Tipo de Evolução</label>
                                    <select id="id_evolucao_tipo" name="id_evolucao_tipo" class="custom-select"
                                        required>
                                    </select>
                                </div> --}}

                                <div class="col-6 form-group">
                                    <label for="data" class="custom-label-form">Data</label>
                                    <input id="data" name="data" class="form-control date" autocomplete="off"
                                        type="text" value="{{ date('d/m/Y') }}" required>
                                </div>

                                <div class="col-6 form-group">
                                    <label for="hora" class="custom-label-form">Hora</label>
                                    <input id="hora" name="hora" class="form-control timing" autocomplete="off"
                                        type="text" value="{{ date('H:i') }}" required>
                                </div>


                                <div class="col-12 form-group">
                                    <label class="custom-label-form">Diagnóstico</label>
                                    <textarea id="diagnostico" name="diagnostico" class="summernote"
                                        required></textarea>
                                </div>

                                <div class="col-12 d-grid">
                                    <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                                </div>
                            </form>
                        </div>

                        <div class="col pl-0 overflow-auto" style="height:500px">
                            <ul id="lista-evolucao-servicos" class="timeline">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
