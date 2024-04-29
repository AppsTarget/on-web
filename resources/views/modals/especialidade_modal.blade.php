<!-- Modal -->
<div class="modal fade" id="especialidadeModal" aria-labelledby="especialidadeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="especialidadeModalLabel">Cadastrar especialidade</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/saude-beta/especialidade/salvar" method="POST">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            @csrf
                            <input id="id" name="id" type="hidden">

                            <div class="col-md-8">
                                <label for="descr" class="custom-label-form">Descrição</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch col text-center" style = "margin-top:30px">
                                    <input id="externo" name="externo" class="checkbox custom-control-input" type="checkbox">
                                    <label for="externo" class="custom-control-label">Externo</label>
                                </div>
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