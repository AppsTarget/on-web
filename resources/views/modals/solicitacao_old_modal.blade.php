<!-- Modal -->
<div class="modal fade" id="solicitacaoModal" aria-labelledby="solicitacaoModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="solicitacaoModalLabel">
                    Solicitação de checkout
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id = "solicitacaoModalForm"
                      class = "container-fluid"
                      method = "POST"
                      action = "/saude-beta/encaminhamento/solicitacao/gravar"
                >
                    @csrf
                    <input id = "id_paciente" name = "id_paciente" type="hidden">
                    <input id = "id_solicitacao" name = "id_solicitacao" type="hidden">
                    <div class="row">
                        <div class="col-7 form-group" style = "margin-bottom:0;display:none">
                            <label for="sol_encaminhante_nome" class="custom-label-form">Para</label>
                            <input id="sol_encaminhante_nome"
                                name="sol_encaminhante_nome"
                                class="form-control autocomplete"
                                placeholder="Digite o profissional..."
                                data-input="#sol_encaminhante_id"
                                data-table="enc2_encaminhantes"
                                data-column="nome_fantasia"
                                type="text"
                                autocomplete="off"
                            >
                            <input
                                id = "sol_encaminhante_id"
                                name = "sol_encaminhante_id"
                                type = "hidden"
                                onchange = "mudaSolEnc();"
                            >
                        </div>
                        <div class="col-12 form-group">
                            <label for="sol_enc_esp_nome" class="custom-label-form">Especialidade</label>
                            <input id="sol_enc_esp_nome"
                                name="sol_enc_esp_nome"
                                class="form-control autocomplete"
                                placeholder="Digite a especialidade..."
                                data-input="#sol_enc_esp_id"
                                data-table="especialidade"
                                data-column="descr"
                                data-filter_col="enc_esp"
                                type="text"
                                autocomplete="off"
                            >
                            <input id="sol_enc_esp_id" name="sol_enc_esp_id" type="hidden">
                        </div>
                    </div>
                    <!--
                    <div class = "row">
                        <div class = "col-12 form-group">
                            <label for="enc_cid_nome" class="custom-label-form">CID</label>
                            <input id="enc_cid_nome"
                                name="enc_cid_nome" 
                                class="form-control autocomplete" 
                                placeholder="Digitar CID..." 
                                data-input="#enc_cid_id" 
                                data-table="cid"
                                data-column="nome"  
                                data-filter="S" 
                                type="text"
                                autocomplete="off"
                            >
                            <input id="enc_cid_id" name="enc_cid_id" type = "hidden">
                        </div>
                    </div>
                    -->
                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type = "button" onclick = "gravarSolicitacao()">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>