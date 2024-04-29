<!-- Modal -->
<div class="modal fade" id="orcamentoConversaoModal" aria-labelledby="orcamentoConversaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-orcamento" role="document">
        <div class="modal-content">
            <div class="modal-body" style="padding:1rem 0 2rem">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="orcamentoConversaoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Gerar Contrato
                        </h6>
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="container">
                    <div class="row wizard-converte mt-3 mx-0">
                        <div class="col wo-etapa selected" data-etapa="1">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-clipboard-list"></i>
                            </div>
                        </div>
                        <div class="col wo-etapa" data-etapa="2">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-credit-card"></i>
                            </div>
                        </div>
                        <div class="col wo-etapa" data-etapa="3">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col wo-etapa-label p-0" data-etapa="1">Modalidades</div>
                        <div class="col wo-etapa-label p-0" data-etapa="2">Formas de Pagamento</div>
                        <div class="col wo-etapa-label p-0" data-etapa="3">Resumo</div>
                    </div>
                </div>

                <input id="convert_orcamento_id"   type="hidden">
                <input id="convert_forma_pag_tipo" type="hidden">

                <div class="wizard-body">
                    <div class="container-fluid selected" data-etapa="1">
                        <div class="row position-relative">
                            <div class="col-6">
                                <h4 class="header-color">Dados do associado</h4>
                                <div class="row m-0">
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Nome</span>
                                            <h5 data-paciente="">
                                                Guilherme Mello
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Convênio</span>
                                            <h5 data-paciente_convenio="">
                                                PARTICULAR
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mid-lane"></div>
                            <div class="col-6">
                                <h4 class="header-color">Dados Gerais</h4>
                                <div class="row m-0">
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Data de Validade</span>
                                            <h5 data-validade="">
                                                00/00/0000
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Profissional Examinador</span>
                                            <h5 data-profissional_exa="">
                                                Janayna
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-0">
                                    <div class="col-12">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Observação</span>
                                            <h5 data-obs=""></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="header-color">Modalidaees</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input id="servico_0" class="custom-control-input conv-orcamento-servico" type="checkbox" onchange="selecionar_todos_cbx($(this), '#table-conv-orcamento-procedimentos')">
                                                            <label for="servico_0" class="custom-control-label"></label>
                                                        </div>
                                                    </th>
                                                    <th width="22.5%">procedimento</th>
                                                    <th width="22.5%">Profissional</th>
                                                    <th width="10%" class="text-right">Dente/Região</th>
                                                    <th width="10%" class="text-right">Face</th>
                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="15%" class="text-right">À Vista (R$)</th>
                                                    <th width="15%" class="text-right">À Prazo (R$)</th>
                                                    {{-- <th width="15%" class="text-right">Total (R$)</th> --}}
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-conv-orcamento-procedimentos" class="table table-hover">
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="70%" class="text-right" colspan="5">Totais</th>
                                                    {{-- <th width="20%" class="text-right">Quantidade</th>
                                                    <th width="20%" class="text-right">Valor</th> --}}
                                                    <th width="15%" class="text-right" data-total_vista="0">Total</th>
                                                    <th width="15%" class="text-right" data-total_prazo="0">Total</th>
                                                </tr>
                                                <tr>
                                                    <th width="70%" class="text-right" colspan="5">Totais Selecionados</th>
                                                    {{-- <th width="20%" class="text-right">Quantidade</th>
                                                    <th width="20%" class="text-right">Valor</th> --}}
                                                    <th width="15%" class="text-right" data-total_vista_selecionado="0">Total</th>
                                                    <th width="15%" class="text-right" data-total_prazo_selecionado="0">Total</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="2">
                        <div class="m-auto">
                            <div class="row" id="conv-orcamento-forma-pag">
                                <div class="col form-group">
                                    <label for="conv_forma_pag" class="custom-label-form">Forma de Pagamento</label>
                                    <select id="conv_forma_pag" name="forma_pag" class="custom-select">
                                        <option value="0">Escolher Forma de Pagamento...</option>
                                    </select>
                                </div>

                                <div class="col form-group" style="display:none">
                                    <label for="conv_financeira_id" class="custom-label-form">Financeira</label>
                                    <select id="conv_financeira_id" name="financeira_id" class="custom-select">
                                        <option value="0">Selecionar Financeira...</option>
                                    </select>
                                </div>

                                <div class="col-md-1 form-group" style="display:none">
                                    <label for="conv_forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                    <input id="conv_forma_pag_parcela" name="forma_pag_parcela" class="form-control text-right" type="number">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="conv_forma_pag_valor" class="custom-label-form">Valor (R$)</label>
                                    <input id="conv_forma_pag_valor" name="forma_pag_valor" class="form-control text-right money-brl" type="text">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="conv_data_vencimento" class="custom-label-form">Vencimento</label>
                                    <input id="conv_data_vencimento" name="conv_data_vencimento" class="form-control date" autocomplete="off" type="text">
                                </div>

                                <div class="col-1 form-group d-grid">
                                    <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_forma_pag_conv(); return false">
                                        <i class="my-icon fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="height:calc(100% - 60px)">
                            <div class="col-md-12 h-100">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="25%">Forma de Pagamento</th>
                                                    <th width="25%">Financeira</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                    <th width="15%">Vencimento</th>
                                                    <th width="5%" class="text-center"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="overflow-auto" style="height:calc(100% - 100px)">
                                        <table id="table-conv-orcamento-forma-pag" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="50%" class="text-right" data-total_pag_pendente="0" colspan="2"></th>
                                                    <th width="15%" class="text-right" data-total_pag_parcela="0">0</th>
                                                    <th width="15%" class="text-right" data-total_pag_valor="0">R$ 0,00</th>
                                                    <th width="20%" colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="3">
                        <div class="row position-relative">
                            <div class="col-6">
                                <h4 class="header-color">Dados do associado</h4>
                                <div class="row m-0">
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Nome</span>
                                            <h5 data-conv_resumo_paciente="">
                                                Guilherme Mello
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Convênio</span>
                                            <h5 data-conv_resumo_paciente_convenio="">
                                                PARTICULAR
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mid-lane"></div>
                            <div class="col-6">
                                <h4 class="header-color">Dados Gerais</h4>
                                <div class="row m-0">
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Profissional Examinador</span>
                                            <h5 data-conv_resumo_profissional_exa="">
                                                Janayna
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-0">
                                    <div class="col-12">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Observação</span>
                                            <h5 data-conv_resumo_obs=""></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="header-color">Modalidades</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="25%">procedimento</th>
                                                    <th width="25%">Profissional</th>
                                                    <th width="10%" class="text-right">Dente/Região</th>
                                                    <th width="10%" class="text-right">Face</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-conv-orcamento-procedimentos-resumo" class="table table-hover">
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="100%" class="text-right" colspan="5" data-total="0">
                                                        Total
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h4 class="header-color">Formas de Pagamento</h4>
                            </div>
                            <div class="col-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="27.5%">Forma de Pagamento</th>
                                                    <th width="27.5%">Financeira</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                    <th width="15%">Vencimento</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-conv-orcamento-forma-pag-resumo" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="55%" class="text-right" colspan="2"></th>
                                                    <th width="15%" class="text-right" data-total_pag_parcela="0">0</th>
                                                    <th width="15%" class="text-right" data-total_pag_valor="0">R$ 0,00</th>
                                                    <th width="15%"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="container">
                    <div class="row position-relative">
                        <input  id="id" type="hidden"  value="0">
                        <button id="voltar-converte"  class="btn btn-primary my-auto ml-auto mr-4 px-5"      onclick="voltar_etapa_wo_converte()" disabled="disabled">Voltar</button>
                        <button id="avancar-converte" class="btn btn-primary my-auto mr-auto ml-4 px-5 show" onclick="avancar_etapa_wo_converte()">Avançar</button>
                        <button id="salvar-converte"  class="btn btn-success my-auto mr-auto ml-4 px-5 show" onclick="finalizar_conversao_orcamento()" style="display:none">Salvar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
