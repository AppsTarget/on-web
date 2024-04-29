<!-- Modal -->
<div class="modal fade" id="pedidoEvolucaoModal" aria-labelledby="pedidoEvolucaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-pedido-evolucao" role="document">
        <div class="modal-content h-100">
            <div class="modal-header">
                <div class="container">
                    <div class="row">
                        <h6 class="modal-title header-color" id="pedidoEvolucaoModalLabel" style="font-size:1.4rem; font-weight:600">
                            Contrato | Nº #000001 - Magali Gobbi
                        </h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size:2rem">&times;</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body h-100" style="padding:1rem 0 2rem">
                <div class="container-fluid">
                    <h5 class="header-color">Dados do associado</h5>
                    <div class="row">
                        <div class="col-6 form-group form-search">
                            <label for="pedido_evolucao_profissional_exa_nome" class="custom-label-form">
                                Profissional Solicitante
                            </label>
                            <input id="pedido_evolucao_profissional_exa_nome"
                                name="pedido_evolucao_profissional_exa_nome"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome do Profissional..."
                                data-input="#pedido_evolucao_profissional_exa_id"
                                data-table="pessoa"
                                data-column="nome_fantasia"
                                data-filter_col="colaborador"
                                data-filter="P"
                                type="text"
                                autocomplete="off"
                                required>
                            <input id="pedido_evolucao_profissional_exa_id" name="pedido_evolucao_profissional_exa_id" type="hidden">
                        </div>
                        <div class="col-3 form-group">
                            <label for="pedido_evolucao_validade" class="custom-label-form">Validade</label>
                            <input id="pedido_evolucao_validade" name="validade" class="form-control date" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-3 form-group">
                            <label for="pedido_evolucao_id_convenio" class="custom-label-form">Convênio</label>
                            <select id="pedido_evolucao_id_convenio" name="pedido_evolucao_id_convenio" class="custom-select">
                                <option value="0">Selecionar Convênio...</option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label for="pedido_evolucao_obs" class="custom-label-form">Observação</label>
                            <textarea id="pedido_evolucao_obs" name="pedido_evolucao_obs" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <div class="container-fluid" style="height:calc(100% - 260px)">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#procedimentos-evolucao" role="tab" aria-controls="home" aria-selected="true">
                                procedimentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#formas-pag-evolucao" role="tab" aria-controls="profile" aria-selected="false">
                                Formas de Pagamento
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content card h-100">
                        <div id="procedimentos-evolucao" class="h-100 tab-pane fade show active" role="tabpanel" aria-labelledby="home-tab">
                            <div class="custom-table h-100">
                                <div class="table-header-scroll">
                                    <table id='tabela-planos'>
                                        <thead>
                                            <tr>
                                                

                                                <th width="45%">Plano</th>
                                                <th width="25%">Profissional</th>


                                                {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                                <th width="12.5%" class="text-right">Pessoas</th>
                                                <th width="12.5%" class="text-right">Valor</th>

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
                        <div id="formas-pag-evolucao" class="h-100 tab-pane fade" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="custom-table h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="27.5%">Forma de Pagamento</th>
                                                <th width="27.5%">Financeira</th>
                                                <th width="15%" class="text-right">Parcelas</th>
                                                <th width="15%" class="text-right">Valor (R$)</th>
                                                <th width="15%">Vencimento</th>
                                                <th width="5%" class="text-center"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="overflow-auto" style="height:calc(100% - 100px)">
                                    <table id="table-pedido-evolucao-forma-pag" class="table table-hover">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-footer-scroll" data-table="#table-pedido-evolucao-forma-pag">
                                    <table class="table table-hover m-0">
                                        <tfoot>
                                            <tr>
                                                <th width="65%" class="text-right" data-total_pag_parcela="0" colspan="3">0</th>
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




            </div>
        </div>
    </div>
</div>
