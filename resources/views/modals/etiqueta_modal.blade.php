<!-- Modal -->
<div class="modal fade" id="etiquetaModal" aria-labelledby="etiquetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="etiquetaModalLabel">Cadastrar Etiqueta</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/saude-beta/etiqueta/salvar" method="POST">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            @csrf
                            <input id="id" name="id" type="hidden">
                            <div class="col">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-2" style="top: 24px;left: -5px;">
                                <input id="cor" name="cor" type="hidden" value="#78909C" required>
                                <div class="colorpalette" data-input_id="#cor"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex">
                    <button type="submit" class="btn btn-target mx-auto my-3 px-5">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>