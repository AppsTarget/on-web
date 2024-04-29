<div class="modal fade" id="tabelas_encaminhamento_modal"  aria-labelledby="tabelasEncaminhamentoModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           <div class="modal-header">
                <h6 class="modal-title header-color header-color" id = "tituloModalEncDetalhe">Encaminhamentos Feitos</h6>   
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <input id="id_agendamento" type="hidden">
                <h2 class="title-encaminhamentos" style="margin-top: 5%">Reabilitação</h2>
                <div class="table-header-scroll" style="margin-top: 1%">
                    <table>
                        <thead>
                            <tr>
                                <th width="40%" class="text-left">Área</th>
                                <th width="30%" class="text-left">Qtd.Semana</th>
                                <th width="30%" class="text-left">Tempo Previsto</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-body-scroll">
                    <table>
                        <tbody id="tbody-encaminhamento">

                        </tbody>
                    </table>
                </div>




                <div class="table-header-scroll" style="margin-top: 1%; width: 100%">
                    <h2 class="title-encaminhamentos" style="margin-top: 5%">Habilitação</h2>
                    <table>
                        <thead>
                            <tr>
                                <th width="40%" class="text-left">Vo2/Testes</th>
                                <th width="30%" class="text-left">Observações</th>
                                <th width="30%" class="text-left">Informações Adicionais</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-body-scroll" style="width: 100%">
                    <table>
                        <tbody id="tbody-encaminhamento-habilitacoes">
                            
                        </tbody>
                    </table>
                </div>

                <div style="width:100%; display:inline-flex; justify-content:space-evenly; margin-top:4%;"> 
                    <button class="button-tabela" onclick='desbloquearplanos($("#criarAgendamentoModal #id").val())'>Aceitar</button>
                    <button class="button-tabela -recusar" onclick="fechaModalTabelaEncaminhamento()">Recusar</button>
                </div>
                


            </div>
        </div>
    </div>
</div>