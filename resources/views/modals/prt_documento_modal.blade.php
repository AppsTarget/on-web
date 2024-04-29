<!-- Modal -->
<div class="modal fade" id="criarDocumentoModal" aria-labelledby="criarDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarDocumentoModalLabel">Gerar Documento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="form_documento" class="row" method="POST" onsubmit="salvar_documento(event)">
                        @csrf
                        <div class="col-md-6 form-group">
                            <label for="select-pasta-mod" class="custom-label-form">Pasta</label>
                            <select id="select-pasta-mod" name="pasta" class="custom-select" required>
                                <option value="1">Fisioterapia e osteopatia</option>
                                <option value="2">Receituario e relatórios</option>
                                <option value="3">Pedidos de exames preventivos</option>
                                <option value="4">Laboratórios</option>
                                <option value="5">Pedidos de exames de imagem</option>
                                <option value="6">Programa semanal ON - periodização</option>
                                <option value="7">ON life track</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="id_doc_modelo" class="custom-label-form">Modelo de Documento</label>
                            <select id="id_doc_modelo" name="id_doc_modelo" class="custom-select" required>
                                <option value="" checked>Selecionar modelo de documento...</option>
                            </select>
                        </div>

                        <div class="col-12 form-group">
                            <h5 class="custom-label-form">Texto do Documento</h5>
                            <textarea id="corpo" name="corpo" class="summernote" required></textarea>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-target my-3 mx-auto px-5">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
