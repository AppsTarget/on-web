<!-- Modal -->
<div class="modal fade" id="criarOrcamentoModal" aria-labelledby="criarOrcamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width:95%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="criarOrcamentoModalLabel">Criar Orçamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form action="/saude-beta/pedido/salvar" method="POST" onsubmit="salvar_orcamento(event)">
                        @csrf

                        <input id="id" name="id" type="hidden">

                        <div class="row">
                            <h5 id="num_pedido" class="col">
                                Número do Pedido
                            </h5>
                            <h5 id="status_orcamento" class="col text-right">
                                Aberto
                            </h5>
                        </div>
                        
                        <div class="row">
                            <div class="col form-group form-search">
                                <label for="profissional_exa_nome" class="custom-label-form">
                                    Profissional Examinador
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

                            <div class="col form-group form-search">
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
                     
                            <div class="col-md-3 form-group">
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
                
                            <div class="col-md-2 form-group">
                                <label for="validade" class="custom-label-form">Validade</label>
                                <input id="validade" name="validade" class="form-control date" autocomplete="off" type="text" required>
                            </div>
                        </div>
                
                        <hr>

                        <div class="row">
                            <h4 class="col-12 header-color">
                                procedimentos
                            </h4>
                        </div>
                
                        <div class="row" id="inputs-procedimentos">
                            <div class="col form-group form-search">
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
                
                            <div class="col form-group form-search">
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
                
                            <div class="col-md-1 form-group">
                                <label for="quantidade" class="custom-label-form">Quantidade</label>
                                <input id="quantidade" name="quantidade" class="form-control money" type="text">
                            </div>
                
                            <div class="col-md-1 form-group">
                                <label for="acresc" class="custom-label-form">Acréscimo</label>
                                <input id="acresc" name="acresc" class="form-control money" type="text">
                            </div>
                
                            <div class="col-md-1 form-group">
                                <label for="desc" class="custom-label-form">Desconto</label>
                                <input id="desc" name="desc" class="form-control money" type="text">
                            </div>
                
                            <div class="col-md-1 form-group">
                                <label for="valor" class="custom-label-form">Valor</label>
                                <input id="valor" name="valor" class="form-control money" type="text">
                            </div>
                
                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_servicos(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="22.5%">Profissional</th>
                                                    <th width="22.5%">procedimento</th>
                                                    <th width="10%" class="text-right">Qtde.</th>
                                                    <th width="10%" class="text-right">Acrésc. (R$)</th>
                                                    <th width="10%" class="text-right">Desc. (R$)</th>
                                                    <th width="10%" class="text-right">Valor (R$)</th>
                                                    <th width="15%" class="text-center"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-orcamento-procedimentos" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="45%" class="text-center" colspan="3"></th>
                                                    <th width="10%" class="text-right">Quantidade</th>
                                                    <th width="10%" class="text-right">Acréscimo</th>
                                                    <th width="10%" class="text-right">Desconto</th>
                                                    <th width="10%" class="text-right">Valor</th>
                                                    <th width="15%" class="text-right">Total</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <hr>
                
                        <div class="row">
                            <h4 class="col-12 header-color">
                                Formas de Pagamento
                            </h4>
                        </div>
                     
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
                
                            <div class="col-md-2 form-group">
                                <label for="forma_pag_valor" class="custom-label-form">Valor</label>
                                <input id="forma_pag_valor" name="forma_pag_valor" class="form-control money" type="text">
                            </div>
                
                            <div class="col-md-1 form-group">
                                <label for="forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                <input id="forma_pag_parcela" name="forma_pag_parcela" class="form-control" type="number">
                            </div>
                
                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_forma_pag(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                
                        </div>
                     
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="30%">Forma de Pagamento</th>
                                                    <th width="30%">Financeiro</th>
                                                    <th width="15%" class="text-right">Valor (R$)</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="10%" class="text-center"></th>
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
                                                    <th width="60%" class="text-center" colspan="2"></th>
                                                    <th width="15%" class="text-right">0.0</th>
                                                    <th width="15%" class="text-right">0</th>
                                                    <th width="10%" class="text-center"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <hr>
                
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="obs" class="custom-label-form">Observações</label>
                                <textarea id="obs" name="obs" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                
                        <div class="row">
                            <button id="btn-cancelar-pedido" class="btn btn-danger mx-4 my-3 px-6" onclick="mudar_status_pedido('C'); return false;">
                                Cancelar
                            </button>

                            <button id="btn-salvar-pedido" type="submit" class="btn btn-target ml-auto my-3 px-6">
                                Salvar
                            </button>

                            <button id="btn-concluir-pedido" class="btn btn-success mx-4 my-3 px-6" onclick="mudar_status_pedido('E'); return false;">
                                Concluir Orçamento
                            </button>

                            <button id="btn-finalizar-pedido" class="btn btn-success ml-auto my-3 px-6" onclick="prosseguir_pedido(); return false;">
                                Finalizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>