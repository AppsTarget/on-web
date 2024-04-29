<!-- Modal -->
<div class="modal fade" id="formaPagModal" aria-labelledby="formaPagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/saude-beta/forma-pag/salvar" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="formaPagModalLabel">Cadastrar Forma de Pagamento</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="max_parcelas" class="custom-label-form">Max de Parcelas *</label>
                                <input id="max_parcelas" name="max_parcelas" class="form-control" autocomplete="off" type="number" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="dias_entre_parcela" class="custom-label-form">Dias Entre Parc. *</label>
                                <input id="dias_entre_parcela" name="dias_entre_parcela" class="form-control" autocomplete="off" type="number" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="avista_prazo" class="custom-label-form">Forma</label>
                                <select id="avista_prazo" name="avista_prazo" class="custom-select">
                                    <option value="V">À Vista</option>
                                    <option value="P">À Prazo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="id" name="id" class="btn btn-target m-auto px-5" type="submit">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
