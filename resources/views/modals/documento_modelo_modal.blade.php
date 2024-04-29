<!-- Modal -->
<div class="modal fade" id="criarDocModeloModal" aria-labelledby="criarDocModeloModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarDocModeloModalLabel">
                    Criar Modelo de Documento
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="row" action="/saude-beta/documento-modelo/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="col-md-9 form-group">
                            <label for="titulo" class="custom-label-form">Título do Modelo</label>
                            <input id="titulo" name="titulo" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="ativo" class="custom-label-form">Ativo</label>
                            <select id="ativo" name="ativo" class="custom-select">
                                <option value="1" checked>Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div class="col-12 form-group">
                            <h5 class="custom-label-form">Conteúdo</h5>
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