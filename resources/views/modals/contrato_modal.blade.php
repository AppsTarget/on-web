<!-- Modal -->
<div class="modal fade" id="contratoModal"  aria-labelledby="contratoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-contrato" role="document">
        <div class="modal-content" style="background: white;">
            <div class="modal-body" style="padding:1rem 0 2rem">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="contratoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Proposta de Tratamento
                        </h6>
                        <div class="col d-flex">
                            <span id="status-contrato" class="tag-contrato-aberto">Aberto</span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>

                <input id="contrato_id"   type="hidden">
                <input id="contrato_forma_pag_tipo" type="hidden">

                <div class="container">
                    <div class="row wizard-contrato mt-3 mx-0">
                        <div class="col-3 wo-etapa selected" data-etapa="1">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-user"></i>
                            </div>
                        </div>
                        <div class="col-3 wo-etapa" data-etapa="2">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-clipboard-list"></i>
                            </div>
                        </div>
                        <div class="col-3 wo-etapa" data-etapa="3">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-credit-card"></i>
                            </div>
                        </div>
                        <div class="col wo-etapa" data-etapa="4">
                            <div class="rounded-icon">
                                <i class="my-icon fal fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col wo-etapa-label p-0 selected" data-etapa="1">Informações Básicas</div>
                        <div class="col wo-etapa-label p-0"          data-etapa="2">Modalidades</div>
                        <div class="col wo-etapa-label p-0"          data-etapa="3">Formas de Pagamento</div>
                        <div class="col wo-etapa-label p-0"          data-etapa="4">Resumo</div>
                    </div>
                </div>

                <div class="wizard-body">
                    <div class="container-fluid selected" data-etapa="1">
                        <h5 class="header-color">Dados do associado</h5>
                        <div class="row">
                            <div class="col-8 form-group form-search">
                                <label for="contrato_paciente_nome" class="custom-label-form">Associado</label>
                                <input id="contrato_paciente_nome"
                                    name="contrato_paciente_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do associado..."
                                    data-input="#contrato_paciente_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="paciente"
                                    data-filter="S"
                                    type="text"
                                    autocomplete="off"
                                    required
                                    @if (isset($pessoa))
                                        value="{{ $pessoa->nome_fantasia }}"
                                        readonly
                                    @endif>
                                <input id="contrato_paciente_id" name="contrato_paciente_id" type="hidden"
                                    @if (isset($pessoa))
                                        value="{{ $pessoa->id }}"
                                        readonly
                                    @endif>
                            </div>
                            <div class="col-4 form-group">
                                <label for="contrato_id_convenio" class="custom-label-form">Convênio</label>
                                <select id="contrato_id_convenio" name="contrato_id_convenio" class="custom-select">
                                    <option value="0">Selecionar Convênio...</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="header-color">Dados Gerais</h5>
                        <div class="row">
                            <div class="col-8 form-group form-search">
                                <label for="contrato_profissional_exa_nome" class="custom-label-form">
                                    Profissional Solicitante
                                </label>
                                <input id="contrato_profissional_exa_nome"
                                    name="contrato_profissional_exa_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do Profissional..."
                                    data-input="#contrato_profissional_exa_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="colaborador"
                                    data-filter="P"
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="contrato_profissional_exa_id" name="contrato_profissional_exa_id" type="hidden">
                            </div>
                            <div class="col-4 form-group">
                                <label for="contrato_validade" class="custom-label-form">Validade</label>
                                <input id="contrato_validade" name="validade" class="form-control date" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-12 form-group">
                                <label for="contrato_obs" class="custom-label-form">Observação</label>
                                <textarea id="contrato_obs" name="contrato_obs" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="2">
                        <div class="row" id="inputs-procedimentos">
                            <div class="col-3 form-group form-search">
                                <label for="procedimento_descr" class="custom-label-form">
                                    procedimento
                                </label>
                                <input id="procedimento_descr"
                                    name="procedimento_descr"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do procedimento..."
                                    data-input="#procedimento_id"
                                    data-table="procedimento_preco"
                                    data-column="descr"
                                    @if (getEmpresaObj()->mod_trava_proc_tabela_preco)
                                        data-filter_col="convenio.id"
                                        data-filter=""
                                    @endif
                                    type="text"
                                    autocomplete="off">
                                <input id="procedimento_id" name="procedimento_id" type="hidden">
                            </div>

                            <div class="col-3 form-group form-search pl-0">
                                <label for="profissional_exe_nome" class="custom-label-form">
                                    Profissional
                                </label>
                                <input id="profissional_exe_nome"
                                    name="profissional_exe_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do Profissional..."
                                    data-input="#profissional_exe_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="colaborador"
                                    data-filter="P"
                                    type="text"
                                    autocomplete="off">
                                <input id="profissional_exe_id" name="profissional_exe_id" type="hidden">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="acrescimo" class="custom-label-form">Acréscimo</label>
                                <input id="acrescimo" name="acrescimo"  class="form-control text-uppercase" type="text">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="taxa_plano" class="custom-label-form">Taxa Plano</label>
                                <input id="taxa_plano" name="taxa_plano"  class="form-control text-uppercase" type="text">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="desconto_perc" class="custom-label-form">Desconto (%)</label>
                                <input id="desconto_perc" name="desconto_perc"  class="form-control text-uppercase" type="text">
                            </div>
 
                            <div class="col-1 form-group pr-0">
                                <label for="desconto" class="custom-label-form">Desconto (R$)</label>
                                <input id="desconto" name="desconto"  class="form-control text-right" type="number">
                            </div>

                            <div class="col form-group pr-0">
                                <label for="valor" class="custom-label-form">Valor À Vista</label>
                                <input id="valor" name="valor" class="form-control text-right money-brl" type="text">
                            </div>

                            <div class="col form-group pr-0">
                                <label for="valor_prazo" class="custom-label-form">Valor À Prazo</label>
                                <input id="valor_prazo" name="valor_prazo" class="form-control text-right money-brl" type="text">
                            </div>

                            <div class="col-11 form-group">
                                <label for="procedimento_obs" class="custom-label-form">Observação</label>
                                <input id="procedimento_obs" name="procedimento_obs"  class="form-control text-uppercase" type="text">
                            </div>

                            <div class="col-1 form-group d-grid pr-0">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_contrato_servicos();">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row" style="height:calc(100% - 60px)">
                            <div class="col-md-12 h-100 pr-0">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
            

                                                    <th width="45%">procedimento</th>
                                                    <th width="25%">Profissional</th>


                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="12.5%" class="text-right">À Vista (R$)</th>
                                                    <th width="12.5%" class="text-right">À Prazo (R$)</th>

                                                    {{-- <th width="10%" class="text-right">Total (R$)</th> --}}
                                                    <th width="5%"  class="text-center"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="overflow-auto" style="height:calc(100% - 100px)">
                                        <table id="table-contrato-procedimentos" class="table table-hover">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="table-footer-scroll" data-table="#table-contrato-procedimentos">
                                        <table class="table table-hover m-0">
                                            <tfoot>
                                                <tr>
                                                    <th width="70%" class="text-center" colspan="4"></th>
                                                    <th width="12.5%" class="text-right" data-total_vista="0">Total À Vista</th>
                                                    <th width="12.5%" class="text-right" data-total_prazo="0">Total À Prazo</th>
                                                    <th width="5%"  class="text-right"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="3">
                        <div class="m-auto">
                            <div class="row" id="contrato-forma-pag">
                                <div class="col form-group">
                                    <label for="contrato_forma_pag" class="custom-label-form">Forma de Pagamento</label>
                                    <select id="contrato_forma_pag" name="forma_pag" class="custom-select">
                                        <option value="0">Escolher Forma de Pagamento...</option>
                                    </select>
                                </div>

                                <div class="col form-group" style="display:none">
                                    <label for="contrato_financeira_id" class="custom-label-form">Financeira</label>
                                    <select id="contrato_financeira_id" name="financeira_id" class="custom-select">
                                        <option value="0">Selecionar Financeira...</option>
                                    </select>
                                </div>

                                <div class="col-md-1 form-group" style="display:none">
                                    <label for="contrato_forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                    <input id="contrato_forma_pag_parcela" name="forma_pag_parcela" class="form-control text-right" type="number">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="contrato_forma_pag_valor" class="custom-label-form">Valor (R$)</label>
                                    <input id="contrato_forma_pag_valor" name="forma_pag_valor" class="form-control text-right money-brl" type="text">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="contrato_data_vencimento" class="custom-label-form">Vencimento</label>
                                    <input id="contrato_data_vencimento" name="contrato_data_vencimento" class="form-control date" autocomplete="off" type="text">
                                </div>

                                <div class="col-1 form-group d-grid">
                                    <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_forma_pag_contrato(); return false">
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
                                        <table id="table-contrato-forma-pag" class="table table-hover">
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

                    <div class="container-fluid" data-etapa="4">
                        <div class="row position-relative">
                            <div class="col-6">
                                <h4 class="header-color">Dados do associado</h4>
                                <div class="row m-0">
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Nome</span>
                                            <h5 data-resumo_paciente="">
                                                Guilherme Mello
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Convênio</span>
                                            <h5 data-resumo_paciente_convenio="">
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
                                            <h5 data-resumo_validade="">
                                                00/00/0000
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Profissional Examinador</span>
                                            <h5 data-resumo_profissional_exa="">
                                                Janayna
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-0">
                                    <div class="col-12">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Observação</span>
                                            <h5 data-resumo_obs=""></h5>
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
                                                    <th width="10%" class="text-right">Dente/Região</th>
                                                    <th width="10%" class="text-right">Face</th>
                                                    <th width="25%">procedimento</th>
                                                    <th width="25%">Profissional</th>
                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="15%" class="text-right">À Vista (R$)</th>
                                                    <th width="15%" class="text-right">À Prazo (R$)</th>
                                                    {{-- <th width="15%" class="text-right">Total (R$)</th> --}}
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-resumo-contrato-procedimentos" class="table table-hover">
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="70%" class="text-center" colspan="4"></th>
                                                    {{-- <th width="20%" class="text-right">Quantidade</th>
                                                    <th width="20%" class="text-right">Valor</th> --}}
                                                    <th width="15%" class="text-right" data-total_vista="0">Total</th>
                                                    <th width="15%" class="text-right" data-total_prazo="0">Total</th>
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
                                        <table id="table-contrato-forma-pag-resumo" class="table table-hover">
                                            <tbody>
                                            </tbody>
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
                        <button id="voltar-contrato"  class="btn btn-primary my-auto ml-auto mr-4 px-5"      onclick="voltar_etapa_wo_contrato()" disabled="disabled">Voltar</button>
                        <button id="avancar-contrato" class="btn btn-primary my-auto mr-auto ml-4 px-5 show" onclick="avancar_etapa_wo_contrato()">Avançar</button>
                        <button id="salvar-contrato"  class="btn btn-success my-auto mr-auto ml-4 px-5 show" onclick="salvar_contrato()" style="display:none">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
