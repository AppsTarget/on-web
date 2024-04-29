
<!-- Modal -->
<div class="modal fade" id="cadastrarTituloPagarModal" aria-labelledby="cadastrarTituloPagarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="cadastrarTituloPagarModalLabel">Cadastrar Título a Pagar</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-3 form-group d-flex" style="justify-content: space-between">
                                <div class="tipo-titulo-selected" style="width: 95px; cursor: pointer" onclick="setarTipoTituloPagar('unico')" data-tipo="titulo-unico">
                                    <span>
                                        Título Único
                                    </span>
                                </div>
                                <div style="width: 128px; cursor: pointer" onclick="setarTipoTituloPagar('parcelado')" data-tipo="parcelado">
                                    <span>
                                        Título Parcelado
                                    </span>
                                </div>
                            </div>
                            <div class="col-9 form-group"></div>
                            <div class="col-5 form-group">
                                <label for="n-documento" class="custom-label-form">N. Documento:</label>
                                <input id="n-documento" class="custom-form form-control" type="text">
                            </div>
                            <div class="col-7 form-group">
                                <label for="descr-recebimento" class="custom-label-form">Descrição do Recebimento:</label>
                                <input id="descr-recebimento" class="custom-form form-control" type="text">
                            </div>
                            <div class="col-8">
                                <label for="devedor_nome" class="custom-label-form">Pessoa</label>   
                                <input id="devedor_nome"
                                    name="devedor_nome"  
                                    class="form-control autocomplete" 
                                    placeholder="Digitar Nome do associado..."
                                    data-input="#devedor_id"
                                    data-table="pessoa" 
                                    data-column="nome_fantasia" 
                                    data-filter_col="devedor"
                                    data-filter="S"
                                    type="text" 
                                    autocomplete="off"
                                    required>
                                <input id="devedor_id" name="devedor_id" type="hidden">
                            </div>
                            <div class="col-4 form-group">
                                <label for="valor-titulo-pagar" class="custom-label-form">Valor:</label>
                                <input id="valor-titulo-pagar" class="custom-form form-control money-brl text-right" type="text">
                            </div>
                            <div class="col-md-3 form-group -p">
                                <label for="data-emissao" class="custom-label-form">Emissão</label>
                                <input id="data-emissao" name="data-emissao" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
                            </div>
                            <div class="col-md-3 form-group -p">
                                <label for="data-vencimento" class="custom-label-form">Vencimento</label>
                                <input id="data-vencimento" name="data-vencimento" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
                            </div>
                            <div class="col-2 form-group -p" style="padding: 0px 90px 0px 5px;">
                                <label id="id-parcelas-t-pagar" for="n-parcela" class="custom-label-form">N. Parcela</label>
                                <input id="n-parcela" name="n-parcela" class="form-control" type="number">
                            </div>
                            <div class="col-1 form-group" style="display: none">
                                <div style="cursor: pointer;background-color: #0f67d5;color: white;opacity: 1;padding: 10px 20px 6px 50%;border-radius: 2px;text-transform: uppercase;margin-left: -152%;margin-top: 38%;position: relative;top: 2px;" onclick="CalcularParcelasTitulosPagar()">
                                    Calcular
                                </div>
                            </div>
                        </div>
                        <div id="table-parcelas" style="display: none">
                            <div class="table-header-scroll">
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="10%">
                                                Parcela
                                            </th>
                                            <th width="30%">
                                                Descrição
                                            </th>
                                            <th class="text-right" width="30%">
                                                Valor
                                            </th>
                                            <th class="text-right" width="30%">
                                                Vencimento
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="table-body-scroll">
                                <table>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="button" onclick="salvarTituloPagar()" class="btn btn-target mx-auto my-3 px-5">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>