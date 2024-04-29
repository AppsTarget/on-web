<!-- Modal -->
<div class="modal fade" id="zapsignPessoaModal" aria-labelledby="zapsignPessoaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="zapsignPessoaModalLabel">
                    Informações para o contrato
                    <small class="invalid-feedback"></small>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div id="loading-contrato"
                        style="opacity: 0.8;display: none;justify-content: center;width: 100%;height: 500%;margin-top: -40px;">
                        <div>
                            <div>
                                <img style="height: 200px"
                                    src="http://vps.targetclient.com.br/saude-beta/img/logo_topo_limpo_on.png">
                            </div>
                            <div class='d-flex' style='justify-content: center;margin-bottom: 35px;'>
                                <div class="loader" style="width: 100px; height: 100px"></div>
                            </div>
                        </div>
                    </div>
                    <input id="id_pedido" type="hidden">
                    <input id="id_pessoa" type="hidden">
                    <section id="section-1">
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="nome" class="custom-label-form">Nome</label>
                                <input id="nome" name="nome" class="form-control">
                            </div>
                            <div class="col-3 form-group">
                                <label for="cpf" class="custom-label-form">CPF</label>
                                <input id="cpf" class="form-control" name="cpf" data-mask="000.000.000-00"
                                    data-mask-reverse="true" maxlength="11" type="text">
                            </div>
                            <div class="col-3 form-group">
                                <label for="rg" class="custom-label-form">RG</label>
                                <input id="rg" name="rg" data-mask="AA.AAA.AAA-A" data-mask-reverse="true"
                                    class="form-control">
                            </div>
                            <div class="col-5 form-group">
                                <label for="cep" class="custom-label-form">CEP</label>
                                <input id="cep" class="form-control" name="cep" data-mask="00000-000"
                                    data-mask-reverse="true" maxlength="8" type="text">
                            </div>
                            <div class="col-4 form-group">
                                <label for="cidade" class="custom-label-form">Cidade</label>
                                <input id="cidade" class="form-control" name="cidade" type="text" disabled>
                            </div>
                            <div class="col-3 form-group">
                                <label for="uf" class="custom-label-form">Estado</label>
                                <input id="uf" class="form-control" name="uf" type="text" disabled>
                            </div>

                            <div class="col-10 form-group">
                                <label for="endereco" class="custom-label-form">Endereço</label>
                                <input id="endereco" name="endereco" class="form-control" disabled>
                            </div>
                            <div class="col-2 form-group">
                                <label for="numero" class="custom-label-form">Número</label>
                                <input id="numero" name="numero" class="form-control">
                            </div>
                            <div class="col-8 form-group">
                                <label for="bairro" class="custom-label-form">Bairro</label>
                                <input id="bairro" name="bairro" class="form-control" disabled>
                            </div>
                            <div class="col-4 form-group">
                                <label for="complemento" class="custom-label-form">Complemento</label>
                                <input id="complemento" name="complemento" class="form-control">
                            </div>
                            <div class="col-6 form-group">
                                <label for="email" class="custom-label-form">Email</label>
                                <input id="email" name="email" class="form-control" disabled>
                            </div>
                            <div class="col-4 form-group">
                                <label for="celular" class="custom-label-form">Celular</label>
                                <input id="celular" name="celular" class="form-control">
                            </div>
                            <div class="col-2 form-group d-flex" style="align-items: end">
                                <div id="botao-zap-sign-modal" style="cursor:pointer;height: 41px;line-height: 41px;text-align: center;background: #028b02;width: 100%;color: white;border-radius: 2px;"
                                    onclick="gerarContratoZapSign($('#zapsignPessoaModal #id_pedido').val(), $('#zapsignPessoaModal #id_pessoa').val())">
                                    Enviar Contrato
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="section-2">

                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
