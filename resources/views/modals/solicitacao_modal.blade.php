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
                        <div class="col-6 form-group">
                            <label for="data" class="custom-label-form">Retorno:</label>
                            <input id="sol_enc_ret" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                        </div>
                        <div class="col-6 form-group">
                            <label for="sol_enc_dpt" class="custom-label-form">Departamento:</label>
                            <select id = "sol_enc_dpt" onchange = "control_solicitacao(this)" class = "form-control">
                                <option value = "H">HABILITAÇÃO</option>
                                <option value = "M">MEDICINA</option>
                                <option value = "N">NUTRIÇÃO</option>
                                <option value = "R">REABILITAÇÃO</option>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="sol_enc_esp" class="custom-label-form">Especialidade:</label>
                            <select id = "sol_enc_esp" onchange = "control_solicitacao(this)" class = "form-control">
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="sol_enc_prc" class="custom-label-form">Procedimento:</label>
                            <select id = "sol_enc_prc" onchange = "control_solicitacao(this)" class = "form-control">
                            </select>
                        </div>
                        <div class="col-6 form-group" style = "display:none">
                            <label for="sol_enc_vzs" class="custom-label-form">Vezes por semana:</label>
                            <input type = "text" class = "form-control" id = "sol_enc_vzs" />
                        </div>
                        <div class="col-6 form-group" style = "display:none">
                            <label for="sol_enc_drc" class="custom-label-form">Duração prevista:</label>
                            <input type = "text" class = "form-control" id = "sol_enc_drc" />
                        </div>
                        <div class="col-12 form-group" style = "display:none">
                            <label for="sol_enc_tst" class="custom-label-form">Testes:</label>
                            <select id = "sol_enc_tst" class = "form-control" multiple style = 'height:100px' onchange = "mostraEsp(this.value)">
                                <option value = "c1">VO2 ESPECÍFICO</option>
                                <option value = "c2">VO2 BASAL</option>
                                <option value = "c3">VO2 SUBMÁXIMO</option>
                                <option value = "c4">TESTE DE FORÇA (DINAMOMETRIA)</option>
                                <option value = "c5">TESTE DE MOVIMENTO (CINEMÁTICA)</option>
                            </select>
                        </div>
                        <div class="col-12 form-group" style = "display:none">
                            <label for="sol_enc_spr" class="custom-label-form">Esporte:</label>
                            <input type = "text" class = "form-control" id = "sol_enc_spr" />
                        </div>
                        <div class="col-12 form-group" style = "display:none">
                            <label for="sol_enc_prt" class="custom-label-form">Parte:</label>
                            <select class = "form-control" id = "sol_enc_prt">
                                <option value = "sup">Superior</option>
                                <option value = "inf">Inferior</option>
                            </select>
                        </div>
                        <div class="col-12 form-group" style = "display:none">
                            <label for="sol_enc_obs" class="custom-label-form">Observações:</label>
                            <textarea class = "form-control" id = "sol_enc_obs" style = 'height:100px'></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type = "button" onclick = "gravarSolicitacao()">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>