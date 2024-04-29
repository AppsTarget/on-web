<!-- Modal -->
<div class="modal fade" id="visualizarAgendamentosLoteModal" aria-labelledby="visualizarAgendamentosLoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form>
                <input type="hidden" id='id_pedido'>
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="visualizarAgendamentosLoteModalBtn">Agendamentos em Lote</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id='id_agendamento_lote' type='hidden'>
                    <div class="container">
                        <div id='conteudo-lote-agenda' style="display: flex;justify-content: center;flex-wrap: wrap;">
                            <table class="table table-hover">
                                <thead>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="location.reload()">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>