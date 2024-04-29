
<!-- Modal -->
<div class="modal fade" id="criarNotificacaoModal2" aria-labelledby="criarNotificacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarNotificacaoModalLabel">Adicionar Notificação</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 form-group" id="contatos" style="min-width: 50%;">
                                <label for="assunto-notificacao2" class="custom-label-form">Assunto:</label>
                                <input id="assunto-notificacao2" class="custom-form form-control" type="text">
                            </div>
                            <div class="col-12 form-group form-search">
                                <label for="paciente_nome_notificacao2" class="custom-label-form">Associado</label>
                                <input id="paciente_nome_notificacao2" name="paciente_nome_notificacao2" class="form-control autocomplete" 
                                placeholder="Digitar Nome do associado..." 
                                data-input="#paciente_id_notificacao2" 
                                data-table="pessoa" 
                                data-column="nome_fantasia" 
                                data-filter_col="paciente" 
                                data-filter="S" type="text" 
                                autocomplete="off" 
                                required="">
                                <input id="paciente_id_notificacao2" name="paciente_id_notificacao2" type="hidden">
                            </div>
                            <div class="col-6">
                                <div class="custom-control custom-switch">
                                    <input id="publico-notificacao2" name="publico-notificacao2" class="custom-control-input" type="checkbox" onchange="inputPrivadoNotificacao2();" value="on">
                                    <label for="publico-notificacao2" class="custom-control-label">Privado</label>
                                    <img id="travar-escolha-ag-status" class="ico-info" style="position: relative;top: -2px;left: 0px;" src="/saude-beta/img/icone-de-informacao.png">
                                </div>
                            </div>
                            <div id='profissional-notificacao2'class="col-12 form-group form-search" style="display:none">
                                <label for="notificacao_profissional_nome2" class="custom-label-form">
                                    Membro
                                </label>
                                <input id="notificacao_profissional_nome2" name="notificacao_profissional_nome2" class="form-control autocomplete" placeholder="Digitar Nome do Profissional..." 
                                data-input="#notificacao_profissional_id2" 
                                data-table="pessoa" 
                                data-column="nome_fantasia" 
                                data-filter_col="colaborador" 
                                data-filter="P" 
                                type="text" 
                                autocomplete="off" 
                                required="">
                                <input id="notificacao_profissional_id2" name="notificacao_profissional_id2" type="hidden">
                            </div>
                            <div class="col-12 form-group w-100">
                                <label for="notificacao_txt2" class="custom-label-form">Notificação:</label>
                                <textarea id="notificacao_txt2" name="notificacao_txt" class="form-control" type="text" placeholder="..."  style='height: 212px' required=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="button" onclick="salvar_notificacao2()" class="btn btn-target mx-auto my-3 px-5">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>