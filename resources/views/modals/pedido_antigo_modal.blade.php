<!-- Modal -->
<div class="modal fade" id="pedidoAntigoModal" aria-labelledby="pedidoAntigoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-pedido" role="document">
        <div class="modal-content">
            <div class="modal-body" style="padding:1rem 0 2rem">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="pedidoAntigoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Contrato
                        </h6>
                        <div class="col d-flex">
                            <span id="status-pedido" class="tag-pedido-aberto">Aberto</span>
                        </div>
                        <button type="button" class="close" onclick="removePedidosNaoUtilizados();" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>

                <input id="agenda_id"   type="hidden">
                <input id="pedido_forma_pag_tipo" type="hidden">

                <div class="container">
                    <div class="row wizard-pedido mt-3 mx-0">
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
                        <div class="col wo-etapa-label p-0"          data-etapa="2">Planos</div>
                        <div class="col wo-etapa-label p-0"          data-etapa="3">Formas de Pagamento</div>
                        <div class="col wo-etapa-label p-0"          data-etapa="4">Resumo</div>
                    </div>
                </div>

                <div class="wizard-body">
                    <div class="container-fluid selected" data-etapa="1">
                        <div class="row">
                            <div class="col-8 form-group form-search">
                                <label for="pedido_paciente_nome" class="custom-label-form">Associado</label>
                                <input id="pedido_paciente_nome"
                                    name="pedido_paciente_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do associado..."
                                    data-input="#pedidoAntigoModal #pedido_paciente_id"
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
                                <input id="pedido_paciente_id" name="pedido_paciente_id" type="hidden"
                                    @if (isset($pessoa))
                                        value="{{ $pessoa->id }}"
                                        readonly
                                    @endif>
                            </div>
                            <div class="col-4 form-group">
                                <label for="pedido_id_convenio" class="custom-label-form">Convênio</label>
                                <select id="pedido_id_convenio" name="pedido_id_convenio" class="custom-select" onclick="control_cart_convenio();">
                                    <option value='0'>Selecionar convênio...</option>
                                </select>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-8 form-group form-search">
                                <label for="pedido_profissional_exa_nome" class="custom-label-form">
                                    Consultor de vendas
                                </label>
                                <input id="pedido_profissional_exa_nome"
                                    name="pedido_profissional_exa_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do Profissional..."
                                    data-input="#pedidoAntigoModal #pedido_profissional_exa_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="colaborador"
                                    data-filter="R"
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="pedido_profissional_exa_id" name="pedido_profissional_exa_id" type="hidden">
                            </div>
                            <div class="col-4 form-group" style="display: none" >
                                <label style="display: none"  for="carteira_convenio" class="custom-label-form">Nº da carteira</label>
                                <input  style="display: none" id="carteira_convenio" type="text" class="form-control">
                            </div>
                            <div class="col-12 form-group">
                                <label for="pedido_obs" class="custom-label-form">Observação</label>
                                <textarea id="pedido_obs" name="pedido_obs" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="2">
                        <div class="row" id="inputs-procedimentos">
                            <div class="col-3 form-group form-search" style='display:none'>
                                <label for="procedimento_descr" class="custom-label-form">
                                    procedimento
                                </label>
                                <input id="procedimento_descr"
                                    name="procedimento_descr"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do procedimento..."
                                    data-input="#pedidoAntigoModal #procedimento_id"
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

                            {{-- <div class="col-3 form-group form-search pl-0" style='left: 15px;'>
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
                            </div> --}}

                            {{-- <div class="col-1 form-group pr-0">
                                <label for="dente_regiao" class="custom-label-form">Dente/Região</label>
                                <input id="dente_regiao" name="dente_regiao"  class="form-control text-uppercase" type="text">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="dente_face" class="custom-label-form">Face</label>
                                <input id="dente_face" name="dente_face"  class="form-control text-uppercase" type="text">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="quantidade" class="custom-label-form">Quantidade</label>
                                <input id="quantidade" name="quantidade"  class="form-control text-right" type="number">
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
                            </div> --}}
                            <div class="col-md-7 form-group">
                                <label for="id_plano" class="custom-label-form">Planos</label>
                                <select id="id_plano" name="id_convenio" class="custom-select">
                                    <option value="0">Selecionar Plano...</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="count_plano" class="custom-label-form">Quantidade:</label>
                                <input id="count_plano" name="count_plano" class="form-control" value='1' type="number">
                            </div>
                            <div id="planos-dataset" style="display: none"></div>
                            <input id='contador' type="hidden" value='0'>

                            <div class="col-1 form-group d-grid pr-0">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_antigo_lista2(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row" style="height:calc(100% - 60px)">
                            <div class="col-md-12 h-100 pr-0">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table id='tabela-planos'>
                                            <thead>
                                                <tr>
                                                    

                                                    <th width="70%">Plano</th>
                                                    <th width="5%">vigência</th>


                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="10%" class="text-right">Quantidade</th>
                                                    <th width="10%" class="text-right">Valor</th>

                                                    {{-- <th width="10%" class="text-right">Total (R$)</th> --}}
                                                    <th width="5%"  class="text-center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="overflow-auto" style="height:calc(100% - 100px)">
                                        <table id="table-pedido-procedimentos" class="table table-hover">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="table-footer-scroll" data-table="#table-pedido-procedimentos">
                                        <table class="table table-hover m-0">
                                            <tfoot>
                                                <tr>
                                                    <th width="70%" class="text-center" colspan="4"></th>
                                                    {{-- <th width="12.5%" class="text-right" data-total_vista="0">Total À Vista</th> --}}
                                                    <th width="20%" class="text-right" data-total_prazo="0">Valor total: </th>
                                                    <th width="5%" id='valor_total_planos' class="text-right"></th>
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
                            <div class="row" id="pedido-forma-pag">
                                <div class="col form-group">
                                    <label for="pedido_forma_pag" class="custom-label-form">Forma de Pagamento</label>
                                    <select id="pedido_forma_pag" name="forma_pag" class="custom-select">
                                        <option value="0">Escolher Forma de Pagamento...</option>
                                    </select>
                                </div>

                                <div class="col form-group" style="display:none">
                                    <label for="pedido_financeira_id" class="custom-label-form">Financeira</label>
                                    <select id="pedido_financeira_id" name="financeira_id" class="custom-select">
                                        <option value="0">Selecionar Financeira...</option>
                                    </select>
                                </div>

                                <div class="col-md-1 form-group" style="display:none">
                                    <label for="pedido_forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                    <input id="pedido_forma_pag_parcela" name="forma_pag_parcela" class="form-control text-right" type="number">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="pedido_forma_pag_valor" class="custom-label-form">Valor (R$)</label>
                                    <input id="pedido_forma_pag_valor" name="forma_pag_valor" class="form-control text-right money-brl" type="text">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="pedido_data_vencimento" class="custom-label-form">Vencimento</label>
                                    <input id="pedido_data_vencimento" name="pedido_data_vencimento" class="form-control date" autocomplete="off" type="text">
                                </div>

                                <div class="col-1 form-group d-grid">
                                    <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_forma_pag_pedido_antigo(); return false">
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
                                        <table id="table-pedido-forma-pag" class="table table-hover">
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
                                            <h5 id='data-resumo_validade'>
                                                00/00/0000
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Consultor de vendas</span>
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
                                <h4 class="header-color">Planos</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table id="tabela-planos2" class="table table-hover">
                                            <thead>
                                                <th width="45%">Plano</th>
                                                <th width="25%">Vigência</th>


                                                {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                <th width="12.5%" class="text-right">Pessoas</th>
                                                <th width="12.5%" class="text-right">Valor</th>

                                                {{-- <th width="10%" class="text-right">Total (R$)</th> --}}
                                                <th width="5%"  class="text-center"></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
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
                                        <table id="table-pedido-forma-pag-resumo" class="table table-hover">
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
                        <button id="voltar-pedido"  class="btn btn-primary my-auto ml-auto mr-4 px-5"      onclick="voltar_etapa_wo_pedido_antigo()" disabled="disabled">Voltar</button>
                        <button id="avancar-pedido" class="btn btn-primary my-auto mr-auto ml-4 px-5 show" onclick="avancar_etapa_wo_pedido_antigo()">Avançar</button>
                        <button id="salvar-pedido"  class="btn btn-success my-auto mr-auto ml-4 px-5 show" onclick="salvar_pedido_antigo()" style="display:none">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
