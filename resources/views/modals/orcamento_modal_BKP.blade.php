<!-- Modal -->
<div class="modal fade" id="orcamentoModal" aria-labelledby="orcamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-orcamento" role="document">
        <div class="modal-content">
            <div class="modal-body" style="padding:1rem 0 2rem">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="orcamentoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Criar Orçamento
                        </h6>
                        <div class="col d-flex">
                            <span class="tag-pedido-aberto">Aberto</span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="container">
                    <div class="row wizard-orcamento mt-3 mx-0">
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
                                <label for="paciente_nome" class="custom-label-form">Associado</label>
                                <input id="paciente_nome"
                                    name="paciente_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do associado..."
                                    data-input="#paciente_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="paciente"
                                    data-filter="S"
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="paciente_id" name="paciente_id" type="hidden">
                            </div>
                            <div class="col-4 form-group">
                                <label for="id_convenio" class="custom-label-form">Convênio</label>
                                <select id="id_convenio" name="id_convenio" class="custom-select">
                                    <option value="0">Selecionar Convênio...</option>
                                    @foreach ($convenios as $convenio)
                                        <option value="{{ $convenio->id }}">
                                            {{ $convenio->descr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h5 class="header-color">Dados Gerais</h5>
                        <div class="row">
                            <div class="col-8 form-group form-search">
                                <label for="profissional_exa_nome" class="custom-label-form">
                                    Profissional Solicitante
                                </label>
                                <input id="profissional_exa_nome"
                                    name="profissional_exa_nome"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do Profissional..."
                                    data-input="#profissional_exa_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="colaborador"
                                    data-filter="P"
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="profissional_exa_id" name="profissional_exa_id" type="hidden">
                            </div>
                            <div class="col-4 form-group">
                                <label for="validade" class="custom-label-form">Validade</label>
                                <input id="validade" name="validade" class="form-control date" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-12 form-group">
                                <label for="obs" class="custom-label-form">Observação</label>
                                <textarea id="obs" name="obs" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid" data-etapa="2">
                        <div class="row" id="inputs-procedimentos">
                            <div class="col-3 form-group form-search pr-0">
                                <label for="procedimento_descr" class="custom-label-form">
                                    procedimento
                                </label>
                                <input id="procedimento_descr"
                                    name="procedimento_descr"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do procedimento..."
                                    data-input="#procedimento_id"
                                    data-table="procedimento"
                                    data-column="descr"
                                    data-filter_col="id_emp"
                                    data-filter="{{ getEmpresa() }}"
                                    type="text"
                                    autocomplete="off">
                                <input id="procedimento_id" name="procedimento_id" type="hidden">
                            </div>

                            <div class="col-3 form-group form-search pr-0">
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
                                <label for="dente_regiao" class="custom-label-form">Dente/Região</label>
                                <input id="dente_regiao" name="dente_regiao"  class="form-control" type="text">
                            </div>

                            <div class="col-1 form-group pr-0">
                                <label for="dente_face" class="custom-label-form">Face</label>
                                <input id="dente_face" name="dente_face"  class="form-control" type="text">
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

                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_servicos(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row" style="height:calc(100% - 60px)">
                            <div class="col-md-12 h-100">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="25%">procedimento</th>
                                                    <th width="25%">Profissional</th>

                                                    <th width="10%" class="text-right">Dente/Região</th>
                                                    <th width="10%" class="text-right">Face</th>

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
                                        <table id="table-orcamento-procedimentos" class="table table-hover">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="table-footer-scroll" data-table="#table-orcamento-procedimentos">
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
                        <div class="row" id="inputs-forma-pag">
                            <div class="col form-group">
                                <label for="forma_pag_id" class="custom-label-form">Forma de Pagamento</label>
                                <select id="forma_pag_id" name="forma_pag_id" class="custom-select">
                                    <option value="0">Escolher Forma de Pagamento...</option>
                                    @foreach ($forma_pags as $forma_pag)
                                        <option value="{{ $forma_pag->id }}">
                                            {{ $forma_pag->descr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col form-group" style="display:none">
                                <label for="financeira_id" class="custom-label-form">Financeira</label>
                                <select id="financeira_id" name="financeira_id" class="custom-select">
                                    <option value="0">Selecionar Financeira...</option>
                                </select>
                            </div>

                            <div class="col-md-1 form-group">
                                <label for="forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                <input id="forma_pag_parcela" name="forma_pag_parcela" class="form-control text-right" type="number">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="forma_pag_valor" class="custom-label-form">Valor</label>
                                <input id="forma_pag_valor" name="forma_pag_valor" class="form-control text-right money-brl" type="text">
                            </div>

                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_forma_pag(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row" style="height:calc(100% - 60px)">
                            <div class="col-md-12 h-100">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="30%">Forma de Pagamento</th>
                                                    <th width="30%">Financeira</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                    <th width="10%" class="text-center"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="overflow-auto" style="height:calc(100% - 100px)">
                                        <table id="table-orcamento-forma-pag" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-footer-scroll" data-table="#table-orcamento-forma-pag">
                                        <table class="table table-hover m-0">
                                            <tfoot>
                                                <tr>
                                                    <th width="60%" class="text-center" colspan="2"></th>
                                                    <th width="15%" class="text-right">0</th>
                                                    <th width="15%" class="text-right" data-total_pagamento="0.0">0.0</th>
                                                    <th width="10%" class="text-center"></th>
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
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Celular</span>
                                            <h5 data-resumo_paciente_celular="">
                                                (18) 00000-0000
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Data de Nascimento</span>
                                            <h5 data-resumo_paciente_data_nascimento="">
                                                00/00/0000
                                            </h5>
                                        </div>
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Convênio</span>
                                            <h5 data-resumo_paciente_convenio="">
                                                PARTICULAR | Nº 000000
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
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Profissional Examinador</span>
                                            <h5 data-resumo_profissional_exa="">
                                                Janayna
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="resumo-info">
                                            <span class="custom-label-form">Marketing</span>
                                            <h5 data-resumo_marketing="">
                                                Indicação
                                            </h5>
                                        </div>
                                        <div class="resumo-info">
                                            <span class="custom-label-form">CRO</span>
                                            <h5 data-resumo_crm_cro="">
                                                0000000000
                                            </h5>
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
                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="15%" class="text-right">À Vista (R$)</th>
                                                    <th width="15%" class="text-right">À Prazo (R$)</th>
                                                    {{-- <th width="15%" class="text-right">Total (R$)</th> --}}
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-orcamento-procedimentos" class="table table-hover">
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
                                                    <th width="35%">Forma de Pagamento</th>
                                                    <th width="35%">Financeira</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-orcamento-forma-pag" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="70%" class="text-center" colspan="2"></th>
                                                    <th width="15%" class="text-right">0</th>
                                                    <th width="15%" class="text-right" data-total_pagamento="0.0">0.0</th>
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
                    <div class="row">
                        <button id="voltar-orcamento"  class="btn btn-primary my-auto ml-auto mr-4 px-5"      onclick="voltar_etapa_wo()" disabled="disabled">Voltar</button>
                        <button id="avancar-orcamento" class="btn btn-primary my-auto mr-auto ml-4 px-5 show" onclick="avancar_etapa_wo()">Avançar</button>
                        <button id="salvar-orcamento"  class="btn btn-success my-auto mr-auto ml-4 px-5 show" onclick="salvar_orcamento()" style="display:none">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
