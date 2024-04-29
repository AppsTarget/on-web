
<!-- Modal -->
<div class="modal fade" id="verTituloPagarModal" aria-labelledby="verTituloPagarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="verTituloPagarModalLabel">Visualizar</h6>
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
                                        <th width="10%">
                                            Parcela
                                        </th>
                                        <th width="20%">
                                            Entrada
                                        </th>
                                        <th class="text-right" width="20%">
                                            Emissao
                                        </th>
                                        <th class="text-right" width="20%">
                                            Vencimento
                                        </th>
                                        <th class="text-right" width="30%">
                                            Valor
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="table-body-scroll">
                            <table>
                                <tbody>
                                    <tr>
                                        <td width="10%">
                                            1
                                        </td>
                                        <td width="20%">
                                            10/10/2022
                                        </td>
                                        <td class="text-right" width="20%">
                                            10/10/2022
                                        </td>
                                        <td class="text-right" width="20%">
                                            10/10/2022
                                        </td>
                                        <td class="text-right" width="30%">
                                            R$ 250,00
                                        </td>
                                    </tr>
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
        text-align: end
    }

    .cardExibirTitulo {
        padding: 5px 15px 0px 5px;
        background: #0000000a;
        border-right: 10px solid white;
        border-radius: 5px;
    }
</style>