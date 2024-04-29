<!-- Modal -->
<div class="modal fade" id="conversaoCreditoModal" aria-labelledby="conversaoCreditoModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form>
                <input type="hidden" id='id_pedido'>
                <input type="hidden" id='bAntigo'>
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="conversaoCreditoModal">Conversão</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id='id_plano' type='hidden'>
                    <h4 id='data_contrato'class='text-center' style="color: #212529" >Contratado em: 09/09/2022</h4>
                    <h4 id="data_validade" class='text-center' style="color: #212529; margin-bottom: 15px" >Válido até: 09/10/2022</h4>
                    {{-- <h6 id="total" class='text-center'>Total após conversão: R$ 100,00</h6>
                    <h6 id="total" class='text-center'>Converter: R$ 100,00</h6> --}}
                    <section>
                        <div class="container">
                            <div id='conteudo' style="display: flex;justify-content: center;flex-wrap: wrap;">
                                <table class="table table-hover">
                                    <thead>
                                        <th width="40%" class="text-left">Plano</th>
                                        <th width="15%" class="text-right">Converter</th>
                                        <th width="15%" class="text-right">Qtde. restante</th>
                                        <th width="15%" class="text-right">Valor unitário</th>
                                        <th width="15%" class="text-right">Valor Total</th>
                                    </thead>
                                    <tbody id='table-conversao'>
                                        <tr>
                                            <td width="100%%" class="text-left d-flex">
                                                <div style="margin: -1px 10px 0px 0px;width: 5%;">
                                                    <input id="check-plano-" style="width:100%;height:100%" type="checkbox">
                                                </div>
                                                HABILITACAO-FULL ...
                                            </td>
                                            <td width="15%" class="text-center">0</td>
                                            <td width="15%" class="text-center">2</td>
                                            <td width="15%" class="text-right">R$ 1000,00</td>
                                            <td width="15%" class="text-right">R$ 10000,00</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td width="100%" class="text-right">
                                                Total: <span id="valor_total"></span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <h4 id="total-conversao" style="color: #212529;" class="text-right">Total da conversão: R$ 
                                    <span id="valor_total_conversao">0,00</span>
                                </h4>
                            </div>
                        </div>
                    </section>
                    <section>
                    </section>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="converter_creditos();">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>