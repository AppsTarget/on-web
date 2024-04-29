<div class="modal fade" id="resumoVendasModal" aria-labelledby="resumoVendasModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 800px; max-height:100%;">
        <div class="modal-content"
            style="display:flex; flex-direction:column;height:100%; width: 100%; position: relative;">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color">Resumo</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <input id="id_caixa" type="hidden">

                <div style="padding: 20px 35px 35px 35px;color: #195e1a;display: flex;justify-content: space-evenly;">
                    <div class="cardVendasModal">
                        <div class="d-flex">
                            <img class="img-lista-resumo" style="" src="{{ asset('img/dinheiro.png') }}">
                            <h3 style="font-size: 20px;margin-top: 7px;">Dinheiro</h3>
                        </div>
                        <ul>
                            <li>Entrada: R$: 200,00</li>
                            <li>Saída: R$ 100,00</li>
                        </ul>
                    </div>
                    <div class="cardVendasModal">
                        <div class="d-flex">
                            <img class="img-lista-resumo" style=""
                                src="{{ asset('img/pagamento-com-cartao-de-credito.png') }}">
                            <h3 style="color: #1c5fb8;font-size: 20px;margin-top: 7px;">Cartão</h3>
                        </div>
                        <ul>
                            <li style="color: #1c5fb8;">Débito: R$: 200,00</li>
                            <li style="color: #1c5fb8;">Crédito: R$ 100,00</li>
                        </ul>
                    </div>
                    <div class="cardVendasModal">
                        <div class="d-flex">
                            <img class="img-lista-resumo" style=""
                                src="{{ asset('img/transferencia-bancaria.png') }}">
                            <h3 style="font-size: 20px;margin-top: 7px;">Transferências</h3>
                        </div>
                        <ul>
                            <li>DOC/TED/DEF: R$: 200,00</li>
                            <li>Pix: R$ 100,00</li>
                        </ul>
                    </div>
                    <div>
                        <div>
                            <ul>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .img-lista-resumo {
        height: 30px;
        margin: 0px 10px 0px -5px;
    }

    
</style>
