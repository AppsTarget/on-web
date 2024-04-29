<!-- Modal -->
<style type = "text/css">
details > summary:before {
    content: "► ";
}
details[open] > summary:before {
    content: "▼ ";
}
</style>
<div class="modal fade" id="pedidoModal" aria-labelledby="pedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-pedido" role="document">
        <div class="modal-content" style = "user-select:none">
            <div class="modal-body" style="padding:1rem 0 2rem">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="pedidoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Contrato
                        </h6>
                        <div class="col d-flex">
                            <span id="status-pedido" class="tag-pedido-aberto">Aberto</span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>
                <input id="pedido_id"   type="hidden">
                <input id="pedido_forma_pag_tipo" type="hidden">

                <div class="container">
                    <div class="row wizard-pedido mt-3 mx-0">
                        <div class="col-3 wo-etapa selected" data-etapa="1">
                            <div class="rounded-icon" onclick = "irpara_pedido(1, true)">
                                <i class="my-icon fal fa-user"></i>
                            </div>
                        </div>
                        <div class="col-3 wo-etapa" data-etapa="2">
                            <div class="rounded-icon" onclick = "irpara_pedido(2, true)">
                                <i class="my-icon fal fa-clipboard-list"></i>
                            </div>
                        </div>
                        <div class="col-3 wo-etapa" data-etapa="3">
                            <div class="rounded-icon" onclick = "irpara_pedido(3, true)">
                                <i class="my-icon fal fa-credit-card"></i>
                            </div>
                        </div>
                        <div class="col wo-etapa" data-etapa="4">
                            <div class="rounded-icon" onclick = "irpara_pedido(4, true)">
                                <i class="my-icon fal fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-0">
                        <div class="col wo-etapa-label p-0 selected" data-etapa="1">
                            <span class = "lbl_etapa" onclick = "irpara_pedido(1, true)">Informações Básicas</span>
                        </div>
                        <div class="col wo-etapa-label p-0"          data-etapa="2">
                            <span class = "lbl_etapa" onclick = "irpara_pedido(2, true)">Planos</span>
                        </div>
                        <div class="col wo-etapa-label p-0"          data-etapa="3">
                            <span class = "lbl_etapa" onclick = "irpara_pedido(3, true)">Formas de Pagamento</span>
                        </div>
                        <div class="col wo-etapa-label p-0"          data-etapa="4">
                            <span class = "lbl_etapa" onclick = "irpara_pedido(4, true)">Resumo</span>
                        </div>
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
                                    placeholder="Digitar Nome do Associado..."
                                    data-input="#pedido_paciente_id"
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
                                    @else
                                        value=""
                                    @endif onchange = "carregaEncDisponiveis()">
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
                                    data-input="#pedido_profissional_exa_id"
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
                        </div>
                        <details>
                            <summary>Informações de encaminhamento</summary>
                            <div class = "row">
                                <div class="col-8 form-group">
                                    <label for = "tab_solicitacoes" class = "custom-label-form" style = "display:none">Encaminhamentos solicitados</label>
                                    <div class="table-header-scroll" style = "border:1px solid #ced4da;border-top-left-radius:5px;border-top-right-radius:5px;display:none">
                                        <table style = "border-color:#ced4da">
                                            <thead>
                                                <tr>
                                                    <th width="6%" style = "">&nbsp</th>
                                                    <th width="47%" style = "border-right:1px solid #ced4da">Encaminhante</th>
                                                    <th width="47%">Para</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="table-body-scroll custom-scrollbar" style = "border:1px solid #ced4da;display:none;height:auto;border-bottom-left-radius:5px;border-bottom-right-radius:5px">
                                        <table id="tab_solicitacoes" class="table table-hover" style = "table-layout: fixed;margin-bottom:0">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-4 form-group">
                                    <label for="pedido_encaminhante_nome" class="custom-label-form">Encaminhante</label>
                                    <input id="pedido_encaminhante_nome"
                                            name="pedido_encaminhante_nome"
                                            class="form-control autocomplete"
                                            placeholder="Digite o encaminhante"
                                            data-input="#pedido_encaminhante_id"
                                            data-table="enc2_encaminhantes"
                                            data-column="nome_fantasia"
                                            type="text"
                                            autocomplete="off"
                                            required>
                                    <input id="pedido_encaminhante_id" class="limpaSol" name="pedido_encaminhante_id" type="hidden" onchange = "muda_legenda_encaminhante(this.value)">
                                    <span id = "enc_label" class = "custom-label-form" style = "
                                        text-align: right;
                                        width: 100%;
                                        display: block;
                                    ">
                                        Encaminhante não cadastrado? Clique
                                        <a href = "javascript:encaminhanteModal();">aqui</a>
                                        para cadastrar
                                    </span>
                                </div>
                                <div class="col-4 form-group">
                                    <label for="pedido_enc_esp_nome" class="custom-label-form">Para</label>
                                    <input id="pedido_enc_esp_nome"
                                            name="pedido_enc_esp_nome"
                                            class="form-control autocomplete"
                                            placeholder="Digite a especialidade"
                                            data-input="#pedido_enc_esp_id"
                                            data-table="especialidade"
                                            data-column="descr"
                                            type="text"
                                            autocomplete="off"
                                            required>
                                    <input id="pedido_enc_esp_id" class="limpaSol" name="pedido_enc_esp_id" type="hidden">
                                    <input id="pedido_enc_para_id" class="limpaSol" name="pedido_enc_para_id" type="hidden">
                                </div>
                                <div class="col-4 form-group">
                                    <button class="btn btn-primary" onclick="$('#enc_arquivo').trigger('click')" style = "margin-top:25px;width:100% !important">Anexar encaminhamento</button>
                                    <form method = "POST" enctype = "multipart/form-data" style = "display:none" id = "enc_arquivo_form">
                                        {{ csrf_field() }}
                                        <input type="file" name="enc_arquivo" id = "enc_arquivo" />
                                        <input type="text" name="id_paciente" id = "enc_paciente" />
                                        <input type="text" name="id_pedido"   id = "enc_pedido" />
                                    </form>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-8 form-group">
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
                                                onchange=""
                                                >
                                    <input id="enc_cid_id" name="enc_cid_id" class="limpaSol" style = "display:none" type = "text">
                                </div>
                                <div class = "col-4 form-group">
                                    <label for="enc_data" class="custom-label-form">Data</label>
                                    <input id="enc_data" name="enc_data" class="form-control date limpaSol" autocomplete="off" type="text" placeholder="__/__/____">
                                </div>
                                <div class = "col-4 form-group" style = "display:none" id = "infEncBox">
                                    <button class="btn btn-primary" onclick="infEnc()" style = "margin-top:25px;width:100% !important">Informações Adicionais</button>
                                </div>
                            </div>
                        </details>
                    </div>

                    <div class="container-fluid" data-etapa="2">
                        <div class="col custom-control custom-checkbox custom-control-inline mr-2">
                            <input onchange="inserir_planos_pedido(false)" id="listar_valor_associado" name="listar_valor_associado" class="custom-control-input" type="checkbox">
                            <label for="listar_valor_associado" class="custom-control-label">
                                <span style='color: #6c6c6c;'>Listar somente valores para associados?</span>
                            </label>
                        </div>
                        <div class="row" id="inputs-procedimentos">
                            <div class="col-3 form-group form-search" style='display:none'>
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
                            
                            <div class="col-md-7 form-group">
                                <label for="id_plano" class="custom-label-form">Planos</label>
                                <select id="id_plano" name="id_convenio" class="custom-select" onchange = "atualizaValorPlano()">
                                    <option value="0">Selecionar Plano...</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="count_plano" class="custom-label-form">Quantidade:</label>
                                <input id="count_plano" name="count_plano" min="1" class="form-control text-right" value='1' type="number">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="desc_plan" class="custom-label-form">Valor:</label>
                                <input id="desc_plan" min="1" class="form-control money-brl2 text-right" value='' type="text" onchange = "mudaValorPlano()">
                            </div>
                            <div id="planos-dataset" style="display: none"></div>
                            <input id='contador' type="hidden" value='0'>

                            <div class="col-1 form-group d-grid pr-0">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_lista2(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                            
                            {{-- <div id = "botaoVerEnc" style = "padding-top:1.31rem;padding-left:0.4rem;display:none">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 13px);font-size:0.8rem">
                                    Ver encaminhamentos
                                </button>
                            </div> --}}
                            
                        </div>

                        <div class="row" id = "plan-div" style="height:calc(100% - 100px)">
                            <div class="col-md-12 h-100 pr-0">
                                <div class="custom-table h-100">
                                    <div class="table-header-scroll">
                                        <table id='tabela-planos'>
                                            <thead>
                                                <tr>
                                                    

                                                    <th width="70%">Plano</th>
                                                    <th width="5%">Vigência</th>


                                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                    <th width="10%" class="text-right">Qtde.</th>
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
                                    <div class="table-footer-scroll" id = "plan_tot" data-table="#table-pedido-procedimentos" style = "
                                        position:fixed;
                                        bottom:5.5rem;
                                        right:6rem;
                                        background:#FFF;
                                        width:80%
                                    ">
                                        <table class="table table-hover m-0">
                                            <tfoot>
                                                <tr>
                                                    <th width="60%" class="text-center" colspan="4"></th>
                                                    {{-- <th width="12.5%" class="text-right" data-total_vista="0">Total À Vista</th> --}}
                                                    <th width="20%" class="text-right" data-total_prazo="0">Valor total: </th>
                                                    <th width="20%" id='valor_total_planos' class="text-right"></th>
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
                                    <select id="pedido_forma_pag" name="forma_pag" class="custom-select" onchange="control_forma_pag_pedido1()">
                                        <option value="0">Escolher Forma de Pagamento...</option>
                                    </select>
                                </div>

                                <div class="col-3 form-group" style="display: none">
                                    <label for="conta-bancaria" class="custom-label-form">Conta Bancária</label>
                                    <select id="conta-bancaria" name="conta-bancaria" class="custom-select">
                                        @foreach($contas_bancarias AS $conta)
                                            <option value={{$conta->id}}>{{$conta->titular}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2 form-group">
                                    <label for="financeira" class="custom-label-form">Financeira</label>
                                    <select id="financeira" name="forma_pag" class="custom-select">
                                        <option value="0">Escolher Financeira...</option>
                                    </select>
                                </div>

                                <div class="col-2 form-group" style="display:none">
                                    <label for="troco" class="custom-label-form">Troco</label>
                                    <input data-troco="" id="troco" class="form-control" type='text' disabled>
                                </div>

                                <div class="col-2 form-group" style="display:none">
                                    <label for="creditos-pessoa" class="custom-label-form">Créditos</label>
                                    <input data-creditos="" id="creditos-pessoa" class="form-control" type='text' disabled>
                                </div>

                                <div class="col-md-1 form-group" style="display:none">
                                    <label for="pedido_forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                    <input id="pedido_forma_pag_parcela" min='1' name="forma_pag_parcela" class="form-control text-right" type="number">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="pedido_forma_pag_valor" class="custom-label-form">Valor (R$)</label>
                                    <input id="pedido_forma_pag_valor" min="1" name="forma_pag_valor" class="form-control text-right money-brl" onkeyup="control_valor_pedido1()" type="text">
                                </div>

                                <div class="col-md-2 form-group">
                                    <label for="pedido_data_vencimento" class="custom-label-form">Vencimento</label>
                                    <input id="pedido_data_vencimento" name="pedido_data_vencimento" class="form-control date" autocomplete="off" type="text">
                                </div>

                                <div class="col-1 form-group d-grid">
                                    <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_forma_pag_pedido(); return false">
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
                                                <tr>
                                                    <th width="50%" class="text-right" data-total_troco="0" colspan="2"></th>
                                                    <th width="15%"></th>
                                                    <th width="15%"></th>
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
                        <button id="voltar-pedido"  class="btn btn-primary my-auto ml-auto mr-4 px-5"      onclick="voltar_etapa_wo_pedido(true)" disabled="disabled">Voltar</button>
                        <button id="avancar-pedido" class="btn btn-primary my-auto mr-auto ml-4 px-5 show" onclick="avancar_etapa_wo_pedido(0, true)">Avançar</button>
                        <button id="salvar-pedido"  class="btn btn-success my-auto mr-auto ml-4 px-5 show" onclick="salvar_pedido()" style="display:none">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style type = "text/css">
    .wo-etapa .rounded-icon, .wo-etapa-label .lbl_etapa{cursor:pointer}
    .selected .rounded-icon, .selected .lbl_etapa{cursor:default}
</style>

@include("modals/supervisor_modal")
@include("modals/encaminhante_modal")
@include("modals/infsol_modal")