<!-- Modal -->
<div class="modal fade" id="addfilaEsperaModal" aria-labelledby="addfilaEsperaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="addfilaEsperaModalLabel">Adicionar a Fila de Espera</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="row" action="/saude-beta/fila-espera/salvar" method="POST"> 
                    @csrf
                    <div class="container">
                        <div class="row">
                            <div class="col-12 form-group">
                                <input id="id_agendamento" name="id_agendamento" type="hidden">
                                <label for="hora_chegada">Hora de Chegada</label>
                                <input id="hora_chegada" name="hora_chegada" class="form-control timing" placeholder="__:__" autocomplete="off" type="text" required="" maxlength="5">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-target px-5">Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>