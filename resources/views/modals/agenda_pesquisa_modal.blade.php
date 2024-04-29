<!-- Modal -->
<div class="modal fade" id="agendaPesquisaModal" aria-labelledby="agendaPesquisaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="agendaPesquisaModalLabel">Pesquisa de Agendamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input id="buscar-agendamento"
                name="buscar-agendamento"
                class="form-control autocomplete"
                placeholder="Digitar Nome do Associado..."
                data-input="#paciente_id"
                data-table="pessoa"
                data-column="nome_fantasia"
                data-filter_col="paciente"
                data-filter="S"
                type="text"
                autocomplete="off"
                required style="margin-top: 15px;">

                <div class="container" style="margin-top: 25px;">
                    <div class="row">
                        <div id="pesquisa-agendamentos" class="col">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
