<div class="modal fade" id="caixaModal" aria-labelledby="CaixaModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content"
            style="display:flex; flex-direction:column;height:100%; width: 100%; position: relative;">
            <div class="modal-header">
                <div class="d-flex" style="margin-top: -5px">
                    {{-- <img style="width: 40px; height: 40px" src="{{ asset('img/logo_topo_limpo_on.png') }}"> --}}
                    <h3 style="color: #10518c;margin-left: 10px;margin-top: 12px;margin-bottom: -10px;">Caixa</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <input id="id_caixa" type="hidden">
                <div class="d-flex">
                    <div style="width: 50%;padding: 1.5% 0px 0px 2.5%;color: #000000cf;">
                        <h3>Seja Bem Vindo, <span id="nome-usuario"></span></h3>
                        <div class="d-flex">
                            <p style="margin: 8px 0px 0px 0px;">Veja os Movimentos de</p>
                            <input onchange="atualizarCaixaModal()" style="width: 145px;border: none;color: #303030;margin: 0px 0px 0px -5px;position: relative;background: none;" id="data-selecionada" name="data" class="form-control" autocomplete="off" type="date" required>
                        </div>
                        <p id="mensagem1"></p>
                        <p id="mensagem2"></p>
                        <p id="mensagem3"></p>
                        <div style="padding: 0px 15px 130px 15px;color: #195e1a;display: flex;justify-content: space-evenly;margin: -29px -55px 0px -40px;height: 100%;align-items: end;">
                            <div style="width: 100%;display: flex;justify-content: space-evenly;">
                                <div onclick="fecharCaixa(true)" id="div-button-extrato" class="form-caixa -recebe" style="width: 170px;min-width: 170px; max-width: 170px">
                                    <div class="d-flex" style="width: 63%">
                                        <img style="width: 30px; height: 30px;margin-top: 5%;"
                                            src={{ asset('img/comercial.png') }}>
                                        <h3 class="title-caixa" style="margin-top: 13%;font-size: 13px;width: 100%;">Ver Extrato</h3>
                                    </div>
                                </div>
                                
                                <div onclick="listarCaixas()" id="div-button-extrato" class="form-caixa -recebe" style="width: 170px;min-width: 170px; max-width: 170px">
                                    <div class="d-flex" style="width: 63%">
                                        <img style="width: 30px; height: 30px;margin-top: 5%;"
                                            src={{ asset('img/documento.png') }}>
                                        <h3 class="title-caixa" style="margin-top: 13%;font-size: 13px;width: 100%;">Listar Caixas</h3>
                                    </div>
                                </div>
                                
                                <div onclick="fecharCaixa()" id="div-button-fechar" class="form-caixa -recebe" style="display: none" style="width: 170px;min-width: 170px; max-width: 170px">
                                    <div class="d-flex" style="width: 63%">
                                        <img style="width: 27px;height: 27px;margin-top: 6%;position: relative;right: 5px;opacity: 0.6;"
                                            src={{ asset('img/cancelar.png') }}>
                                        <h3 class="title-caixa" style="margin-top: 13%;font-size: 13px;width: 100%;">Fechar Caixa</h3>
                                    </div>
                                </div>

                                
        
                                <div onclick="abrirCaixa()" id="div-button-abrir" class="form-caixa -recebe" style="width: 170px;min-width: 170px; max-width: 170px">
                                    <div class="d-flex" style="width: 63%">
                                        <img style="width: 27px;height: 27px;margin-top: 6%;position: relative;right: 5px;opacity: 0.6;"
                                            src={{ asset('img/correto.png') }}>
                                        <h3 class="title-caixa" style="margin-top: 13%;font-size: 13px;width: 100%;">Abrir Caixa</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 20px 35px 35px 35px;color: #195e1a; width: 50%">
                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="" src="{{ asset('img/dinheiro (1).png') }}">
                                <div class="d-flex" style="width: 100%">
                                    <h3 style="font-size: 20px;margin-top: 7px;">Dinheiro</h3>
                                    <div class="d-flex" style="justify-content: end;width: 100%; align-items: center">
                                        <img onclick="extratoCaixa('dinheiro')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                        <h3 id="total-dinheiro" style="font-size: 20px;margin-top: 7px;color: #000">R$: 200,00</h3>
                                        <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul style="margin: 0">
                                    <li>Saldo Inicial: <span id="saldo-inicial">R$ 200,00</span></li>
                                    <li>Entradas</li>
                                        <li style="margin-left: 30px">Suprimento de Caixa: <span id="suprimento-caixa">R$ 100,00</span></li>
                                        <li style="margin-left: 30px">Recebimento a Vista: <span id="recebimento-vista">R$ 100,00</span></li>
                                        <li style="margin-left: 30px">Recebimento a Prazo: <span id="recebimento-prazo">R$ 100,00</span></li>
                                    <li>Saídas</li>    
                                        <li style="margin-left: 30px">Sangria de Caixa: <span id="sangria-de-caixa">R$ 100,00</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style=""
                                    src="{{ asset('img/pagamento-com-cartao-de-credito.png') }}">
                                    <div class="d-flex" style="width: 100%">
                                        <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Cartão</h3>
                                        <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                            <img onclick="extratoCaixa('cartao')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                            <h3 id="total-cartao" style="font-size: 20px;margin-top: 7px;color: #000;">R$: 200,00</h3>
                                            <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                        </div>
                                    </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul>
                                    <li style="color: #1c5fb8;">Débito: <span id="valor-debito">R$ 200,00</span></li>
                                    <li style="color: #1c5fb8;">Crédito: <span id="valor-credito">R$ 100,00</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="margin: -5px 10px 0px -5px;height: 32px;"
                                    src="{{ asset('img/transferencia-bancaria.png') }}">
                                    <div class="d-flex" style="width: 100%">
                                        <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Transferências</h3>
                                        <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                            <img onclick="extratoCaixa('transferencia')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                            <h3 id="total-transferencias" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                            <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                        </div>
                                    </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul>
                                    <li style="color: #a26c13">Pix: <span id="valor-pix">R$ 100,00</span></li>
                                    <li style="color: #a26c13">Outros: <span id="valor-transferencia">R$ 200,00</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="margin: -5px 10px 0px -5px;height: 32px;"
                                    src="{{ asset('img/transferencia-bancaria.png') }}">
                                    <div class="d-flex" style="width: 100%">
                                        <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Convênio</h3>
                                        <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                            <img onclick="extratoCaixa('convenio')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                            <h3 id="total-convenio" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                            <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                        </div>
                                    </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul id="lista-convenio">
                                </ul>
                            </div>
                        </div>
                        {{-- <div class="cardVendasModal">
                            <div class="d-flex">
                                <img class="img-lista-resumo" style="margin: -5px 10px 0px -5px;height: 32px;"
                                    src="{{ asset('img/transferencia-bancaria.png') }}">
                                    <div class="d-flex" style="width: 100%">
                                        <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Boleto</h3>
                                        <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                            <h3 id="total-boleto" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                            <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="cardVendasModal">
                            <div class="d-flex">
                                <img class="img-lista-resumo" style="margin: -5px 10px 0px -5px;height: 32px;"
                                    src="{{ asset('img/transferencia-bancaria.png') }}">
                                    <div class="d-flex" style="width: 100%">
                                        <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Duplicata</h3>
                                        <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                            <h3 id="total-duplicata" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                            <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                        </div>
                                    </div>
                            </div>
                        </div> --}}

                        <hr>

                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="" src="{{ asset('img/dinheiro (1).png') }}">
                                <div class="d-flex" style="width: 100%">
                                    <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Recebimentos</h3>
                                    <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                        <img onclick="extratoCaixa('recebimentos')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                        <h3 id="total-recebimentos" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                        {{-- <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}"> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul style="margin: 0">
                                    <li>Entrada: R$ 200,00</li>
                                    <li>Saída: R$ 100,00</li>
                                </ul>
                            </div>
                        </div>
                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="" src="{{ asset('img/dinheiro (1).png') }}">
                                <div class="d-flex" style="width: 100%">
                                    <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Vendas</h3>
                                    <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                        <img onclick="extratoCaixa('vendas')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer" src="{{asset('img/olho.png') }}">
                                        <h3 id="total-vendas" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                        {{-- <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}"> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul style="margin: 0">
                                    <li>Entrada: R$ 200,00</li>
                                    <li>Saída: R$ 100,00</li>
                                </ul>
                            </div>
                        </div>
                        <div class="cardVendasModal">
                            <div class="d-flex" style="margin-bottom: 5px;">
                                <img class="img-lista-resumo" style="" src="{{ asset('img/dinheiro (1).png') }}">
                                <div class="d-flex" style="width: 100%">
                                    <h3 style="font-size: 20px;margin-top: 7px;width: 100%">Sangrias e Suprimentos</h3>
                                    <div class="d-flex" style="justify-content: end;width: 100%;  align-items: center">
                                        <img onclick="extratoCaixa('sangria-suprimento')" style="width:20px; height: 20px;margin-right: 10px;position: absolute;left: 60%; cursor: pointer;position: absolute;left: 60%;" src="{{asset('img/olho.png') }}">
                                        <i style="margin-right: 10px;" class="my-icon far fa-edit" onclick="corrigir_valor_caixa()"></i>
                                        <h3 id="total-sangrias-suprimentos" style="font-size: 20px;margin-top: 7px;">R$: 200,00</h3>
                                        <img class="sort-down-caixa" style="width: 15px;height: 15px;margin-left: 5px;" src="{{ asset('img/sort-down.png') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="detalhes-foma-pag-caixa">
                                <ul style="margin: 0">
                                    <li>Entrada: <span id="total-suprimento">R$ 200,00</span></li>
                                    <li>Saída: <span id="total-sangria">R$ 100,00</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<style>
    .table-caixa {
        margin-bottom: 25px;
        width: 81%;
    }

    .data-caixa {
        display: inline-flex !important;
        color: #000000ba !important;
        font-size: 12px !important;
        justify-content: center !important;
        cursor: pointer
    }

    .form-caixa {
        height: 100% !important;
        background: #f7f7f7 !important;
        padding: 5% 5% 0% 5% !important;
        border-radius: 10px !important;
        border: 1px solid #cdcdcd !important;
        cursor: pointer;
        box-shadow: 2px 2px #0000001c !important;
    }

    .cardVendasModal {
        background: #f7f7f7;
        padding: 15px 15px 15px 15px;
        border-radius: 15px;
        width: 100%;
        border: 1px solid #cdcdcd;
        max-height: 65px;
        margin-bottom: 2px;
        filter: grayscale(1) !important;
        transition: max-height 0.3s
    }

    .cardVendasModal p, .cardVendasModal h3 {
        color: #000;
    }

    .style-caixa {
        color: #000000ba !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        margin: -1px 10px 0px 0px !important;
        cursor: pointer
    }

    #div-button-sangria {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #00800038 !important;
        cursor: pointer
    }

    #div-button-fechar {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background-color: #ff000052 !important;
        cursor: pointer
    }

    #div-button-sangria:hover {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #0080009e !important;
    }

    #div-button-fechar:hover {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #ff0000a1 !important;
    }

    #div-button-extrato {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #0000ff40 !important;
    }

    #div-button-extrato:hover {
        background: #0000ffa8 !important;
    }

    .img-lista-resumo:hover {
        opacity: 1;
    }

    #div-button-alterar-saldo {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #ffff0073 !important
    }
    #div-button-alterar-saldo:hover {
        background: #ffff00d4 !important
    }


    .form-caixa:hover {
        filter: brightness(0.9)
    }

    #div-button-abrir {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background-color: #0080007d !important;
        cursor: pointer
    }
    #div-button-abrir:hover {
        width: 205px;
        height: 45px !important;
        padding: 0 !important;
        background: #008000bf !important;
    }

    .detalhes-foma-pag-caixa {
        max-height: 0px;
        overflow: auto; 
        transition: max-height 0.3s
    }
    #caixaModal li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px
    }
</style>
