<!-- Modal -->
<div class="modal fade" id="convenioModal2" aria-labelledby="convenioModal2Label" aria-hidden="true">
    <div class="modal-dialog modal-x" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="convenioModalLabel2">Cadastrar Convênio</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/convenio/salvar" method="POST">
                        @csrf
                        <input id="id2" name="id2" type="hidden">

                        <div class="col-md-8 form-group">
                            <label for="descr2" class="custom-label-form">Descrição *</label>
                            <input id="descr2" name="descr2" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="prazo2" class="custom-label-form">Prazo *</label>
                            <input id="prazo2" name="prazo2" class="form-control" autocomplete="off" type="number" required>
                        </div>
                        <div class="col-md-12 form-group form-search">
                            <div class="custom-control custom-checkbox mt-2 ml-1">
                                <input id="quem-paga" name="quem_paga" class="custom-control-input" type="checkbox">
                                <label for="quem-paga" class="custom-control-label">Cliente Paga?</label>
                            </div>
                            {{-- <label for="cliente_nome" class="custom-label-form">Cliente *</label> --}}
                            <input id="cliente_nome"
                                name="cliente_nome"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome da Pessoa Jurídica..."
                                data-input="#cliente_id"
                                data-table="pessoa"
                                data-column="nome_fantasia"
                                data-filter_col="tpessoa"
                                data-filter="j"
                                type="text"
                                autocomplete="off"
                                required>
                            <input id="cliente_id" name="cliente_id" type="hidden">
                        </div>
                    </form>
                    <button type="button" onclick="criar_convenio();" class="btn btn-target my-3 mx-auto px-5"  style="display:flex">
                        Criar Convenio
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>