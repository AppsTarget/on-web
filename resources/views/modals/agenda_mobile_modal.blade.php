<!-- Modal -->
<div class="modal fade" id="agendaMobileModal" aria-labelledby="agendaMobileModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width:100%; margin-top: 45%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="agendaMobileModal">Agendamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body -mobileagendamentos">    
                <div class="container">
                    <input id="id_agendamento" type="hidden">
                    <input id="id_paciente" type="hidden">
                    <input id="antigo" type="hidden">
                    <div class="col-12">
                        <div>
                            <div class="d-flex" style="justify-content: center">
                                <div style="width: 50%;margin-bottom: 5%;">
                                    <img class="custom-image my-auto" src="/saude-beta/img/pessoa/1.jpg" onerror="this.onerror=null;this.src='/saude-beta/img/paciente_default.png'">
                                </div>
                            </div>
                            <h4 id="nome-paciente">Vinícius Cavani Behlau</h4>

                            <div id="modalidade" class="d-flex" style="width: 120%; margin-bottom: 15px">
                                <div style="width:10%;margin: 0px 0px 0px -15%;">
                                    <img class="custom-image" style="min-height: 0% !important;" src="{{ asset('img/areas/2.png') }}">
                                </div>
                                <p style="padding: 1% 1% 1% 3%;font-size: 125%;color: #110355;font-weight: 600;font-family: system-ui; font-bold: 900">Levantamento de Peso Olímpico</p>
                            </div>
                            <div class="d-flex btn-agenda-modal-mobile" style="margin: 0 -20% 0 -20%;height: 75px;justify-content: space-between;">
                                <button style="background: #b6ec77;border: 2px solid black;padding: 1% 0% 0% 0%;" type="button" onclick="atualizarAtendimento($('#agendaMobileModal #antigo').val())">
                                    <img src="{{ asset('img/agenda_atualizar_atendimento.png') }}" style="width: 37px; margin-bottom: 1px; margin-top: 4px;">
                                    <p  style="color: #000000;font-size: 11px;font-family: system-ui; font-weight: 700; margin-top: 1px">
                                        Atualizar Atendimento
                                    </p>
                                </button>
                                <button style="background: #05d5ef91;border: 2px solid black;padding: 1% 0% 0% 0%;" type="button" onclick="redirect('http://vps.targetclient.com.br/saude-beta/pessoa/prontuario/' + $('#agendaMobileModal #id_paciente').val())">
                                    <img style="width: 40px; margin-bottom: 3px" src="{{ asset('img/historico-medico.png') }}">
                                    <p  style="color: #000000;font-size: 12px;font-family: system-ui; font-weight:700;">
                                        Atualizar Prontuário
                                    </p>
                                    
                                </button>
                                
                            </div>
                           <!-- <div class="d-flex btn-agenda-modal-mobile" style="margin: 0 -20% 0 -20%;height: 45px;justify-content: space-between;">
                                <button id="bloquear-grade-agendamento-mobile" style="background: rgb(255, 96, 96);border: 2px solid black;padding: 0% 0% 0% 0%;margin: auto;margin-top: 14px;height: 30px;" type="button" onclick="">
                                    
                                    <p  style="color: #000000;font-size: 12px;font-family: system-ui; font-weight: 700; margin: 5px 0px 6px 0px;">
                                        Bloquear Horário
                                    </p>
                                </button>
                                
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
