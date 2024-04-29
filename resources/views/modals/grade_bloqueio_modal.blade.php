<!-- Modal -->
<div class="modal fade" id="gradeBloqueioModal" aria-labelledby="gradeBloqueioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="gradeBloqueioModalLabel">Cadastrar Bloqueio de Grade</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/grade-bloqueio/salvar" method="POST">
                        @csrf
                        <input id="id-profissional" name="id_profissional" type="hidden" required>

                        <div class="col-md-6 form-group hide-mobile">
                            <label for="data-inicial" class="custom-label-form">Data Ínicio *</label>
                            <input id="data-inicial" name="data_inicial" class="form-control date" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-6 form-group hide-mobile">
                            <label for="data-final" class="custom-label-form">Data Final *</label>
                            <input id="data-final" name="data_final" class="form-control date" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4 form-group hide-mobile">
                            <label for="dia-semana" class="custom-label-form">Dia da Semana *</label>
                            <select id="dia-semana" name="dia_semana" class="form-control custom-select">
                                <option value="0">Todos</option>
                                <option value="1">Domingo</option>
                                <option value="2">Segunda</option>
                                <option value="3">Terça</option>
                                <option value="4">Quarta</option>
                                <option value="5">Quinta</option>
                                <option value="6">Sexta</option>
                                <option value="7">Sábado</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group hide-mobile">
                            <label for="hora-inicial" class="custom-label-form">Hora Ínicio*</label>
                            <input id="hora-inicial" name="hora_inicial" class="form-control timing" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4 form-group hide-mobile">
                            <label for="hora-final" class="custom-label-form">Hora Fim*</label>
                            <input id="hora-final" name="hora_final" class="form-control timing" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="obs" class="custom-label-form">Motivo</label>
                            <textarea id="obs" name="obs" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-12 text-right mt-3">
                            <button type="button" class="btn btn-target" onclick="salvarGradeBloqueio()">
                                Salvar
                            </button>
                        </div>
                    </form>
                    <hr>
                    <h4 class='hide-mobile'>Bloqueios Ativos</h4>
                    <div id="lista-grade-bloqueio" class="container hide-mobile">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>