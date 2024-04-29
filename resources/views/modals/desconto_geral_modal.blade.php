<!-- Modal -->
<div class="modal fade" id="descontoGeralModal" aria-labelledby="descontoGeralModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="descontoGeralModalLabel">Cadastrar Desconto Geral (%)</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" method="POST" action="/saude-beta/parametros/salvar-desconto-geral">
                        @csrf
                        <input id="id" name="id" type="hidden">
                        
                        <div class="col-md-12">
                            <label for="desconto" class="custom-label-form">Desconto(%) *</label>
                            <input id="desconto" name="desconto" class="form-control" autocomplete="off" type="number" required step='0.001'>
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