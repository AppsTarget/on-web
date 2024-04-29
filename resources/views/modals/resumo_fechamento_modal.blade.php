<div class="modal fade" id="extratoFechamentoModal" aria-labelledby="extratoFechamentoModal" aria-hidden="true" style="margin-left: 12px">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 100%;font-size: 12px;">
        <div class="modal-content"
            style="display:flex; flex-direction:column;height:100%; width: 98%; position: relative;">
            <div class="modal-header">
                <div class="d-flex" style="margin-top: -5px">
                    {{-- <img style="width: 40px; height: 40px" src="{{ asset('img/logo_topo_limpo_on.png') }}"> --}}
                    <h3 style="color: #10518c;margin-left: 10px;margin-top: 12px;margin-bottom: -10px;">Extrato</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <div class="table-header-scroll">
                    <table>
                        <thead id="header-extrato-caixa">
                            <th>
                                Consultor
                            </th>
                            <th>
                            
                            </th>
                        </thead>
                    </table>
                </div>
                <div class="table-body-scroll">
                    <table id="table-extrato-caixa">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3" style="margin: 45px 17% 40px 17% !important;">
                <button id="button-caixa-sangria" class="btn btn-primary m-auto px-5" type="button"
                    onclick="salvarFechamentoCaixa()">
                    Fechar Caixa
                </button>
                <button id="button-caixa-sangria" class="btn btn-primary m-auto px-5" type="button"
                    onclick="sangriaCaixa(true)">
                    Sangria/Suprimento
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    /* .custom-tbody td::after{
        content: "|";
        border-right: 1px solid;
    } */
</style>
