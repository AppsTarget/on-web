
<!-- Modal -->
<div class="modal fade" id="verTituloReceberModal" aria-labelledby="verTituloReceberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="verTituloReceberModalLabel">Visualizar</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-2 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">N. Doc</p>
                            <h4 id="n-doc">001320</h4>
                        </div>
                        <div class="col-6 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Descrição</p>
                            <h4 id="descricao">Impostos</h4>
                        </div>
                        <div class="col-2 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Pago</p>
                            <h4 id="pago">Sim</h4>
                        </div>
                        <div class="col-2 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Data Pagamento</p>
                            <h4 id="d_pago">15/09/2022</h4>
                        </div>


                        
                        <div class="col-4 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Fornecedor</p>
                            <h4 id="fornecedor">Vinícius Cavani Behlau</h4>
                        </div>
                        <div class="col-4 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Criado por</p>
                            <h4 id="criado-por">Vinícius Cavani Behlau</h4>
                        </div>
                        <div class="col-4 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Pago por</p>
                            <h4 id="pago-por">Vinícius Cavani Behlau</h4>
                        </div>
                        


                        <div class="col-3 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Data De Entrada</p>
                            <h4 id="entrada">10/10/2022</h4>
                        </div>
                        <div class="col-3 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Data De Emissao</p>
                            <h4 id="emissao">10/10/2022</h4>
                        </div>
                        <div class="col-3 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Data De Vencimento</p>
                            <h4 id="vencimento">10/12/2021</h4>
                        </div>
                        <div class="col-3 cardExibirTitulo" style="margin-bottom: 25px">
                            <p style="margin: 0 0 3px 0;;font-size: 12px;">Valor Total</p>
                            <h4 id="valor-total">R$ 100,00</h4>
                        </div>



                    </div>
                    <div id="table-parcelas" style="margin: 0px -35px 0px -35px;">
                        <div class="table-header-scroll">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="12%" class="text-left">
                                            Parcela
                                        </th>
                                        <th class="text-right" width="20%">
                                            Entrada
                                        </th>
                                        <th class="text-right" width="20%">
                                            Emissao
                                        </th>
                                        <th class="text-right" width="20%">
                                            Vencimento
                                        </th>
                                        <th class="text-right" width="15%">
                                            Valor
                                        </th>
                                        <th class="text-right" width="8%">
                                            Pago
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
        </div>
    </div>
</div>


<style>
    .cardExibirTitulo h4{
        text-align: end;
        font-size: 20px
    }

    .col-2.cardExibirTitulo {
        max-width: 15.65%;
    }
    .col-4.cardExibirTitulo {
        max-width: 24%
    }
    .cardExibirTitulo {
        padding: 5px 15px 0px 5px;
        background: #00000008;
        border-radius: 7px 7px 0px 0px;
        border: 1px solid;
        margin: 0px 5px 0px 0px;
    }
</style>