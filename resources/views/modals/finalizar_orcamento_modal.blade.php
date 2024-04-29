<!-- Modal -->
<div class="modal fade" id="finalizarOrcamentoModal" aria-labelledby="finalizarOrcamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width:95%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="finalizarOrcamentoModalLabel">Finalizar Orçamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form action="/saude-beta/pedido/finalizar" method="POST" onsubmit="finalizar_orcamento(event)">
                        @csrf

                        <input id="id" name="id" type="hidden">

                        <div class="row">
                            <h5 id="num_pedido" class="col">
                                Número do Pedido
                            </h5>
                        </div>
                        
                        <div class="row">
                            <div class="col form-group form-search">
                                <label for="descr_prof_exa" class="custom-label-form">Profissional Examinador</label>
                                <input id="descr_prof_exa" name="descr_prof_exa" class="form-control" autocomplete="off" type="text" readonly required>
                                <input id="id_prof_exa" name="id_prof_exa" type="hidden" required>
                            </div>

                            <div class="col form-group form-search">
                                <label for="descr_paciente" class="custom-label-form">Associado</label>
                                <input id="descr_paciente" name="descr_paciente" class="form-control" autocomplete="off" type="text" readonly required>
                                <input id="id_paciente" name="id_paciente" type="hidden" required>
                            </div>
                     
                            <div class="col-md-3 form-group">
                                <label for="descr_convenio" class="custom-label-form">Convênio</label>
                                <input id="descr_convenio" name="descr_convenio" class="form-control" autocomplete="off" type="text" readonly required>
                                <input id="id_convenio" name="id_convenio" type="hidden" required>
                            </div>
                
                            <div class="col-md-2 form-group">
                                <label for="validade" class="custom-label-form">Validade</label>
                                <input id="validade" name="validade" class="form-control date" autocomplete="off" type="text" readonly required>
                            </div>
                        </div>
                
                        <hr>

                        <div class="row">
                            <h4 class="col-12 header-color">
                                procedimentos
                            </h4>
                        </div>
                
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-table">
                                    <div class="table-header-scroll">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center"></th>
                                                    <th width="20%">Profissional</th>
                                                    <th width="20%">procedimento</th>
                                                    <th width="10%" class="text-right">Quantidade</th>
                                                    <th width="10%" class="text-right">Acréscimo</th>
                                                    <th width="10%" class="text-right">Desconto</th>
                                                    <th width="10%" class="text-right">Valor</th>
                                                    <th width="15%">Nº Guia</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-finalizar-procedimentos" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <hr>
                     
                        <div class="row">
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
                                <label for="forma_pag_valor" class="custom-label-form">Valor</label>
                                <input id="forma_pag_valor" name="forma_pag_valor" class="form-control money" type="text">
                            </div>
                
                            <div class="col-md-1 form-group">
                                <label for="forma_pag_parcela" class="custom-label-form">Parcelas</label>
                                <input id="forma_pag_parcela" name="forma_pag_parcela" class="form-control" type="number">
                            </div>
                
                            <div class="col-md-2 form-group">
                                <label for="forma_pag_validade" class="custom-label-form">Validade</label>
                                <input id="forma_pag_validade" name="forma_pag_validade" class="form-control date" autocomplete="off" type="text">
                            </div>
                
                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_finalizar_pedido_forma_pag(); return false">
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
                                                    <th width="25%">Forma de Pagamento</th>
                                                    <th width="25%">Financeiro</th>
                                                    <th width="15%" class="text-right">Valor</th>
                                                    <th width="15%" class="text-right">Parcelas</th>
                                                    <th width="15%">Validade</th>
                                                    <th width="5%"  class="text-center"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div>
                                        <table id="table-finalizar-forma-pag" class="table table-hover">
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <hr>
                
                
                        <div class="row">
                            <button id="btn-cancelar-pedido" class="btn btn-secondary mx-auto my-3 px-6" 
                                    onclick="$('#finalizarOrcamentoModal').modal('hide'); $('#criarOrcamentoModal').modal('show'); return false;">
                                Voltar
                            </button>

                            <button id="btn-finalizar-pedido" class="btn btn-success mx-auto my-3 px-6" type="submit">
                                Finalizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>