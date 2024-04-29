<div class="modal fade" id="encaminhamento_modal"  aria-labelledby="EncaminhamentoModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           <div class="modal-header">
                <h6 class="modal-title header-color header-color">Encaminhamento</h6>   
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <input id="id_agendamento" type="hidden">
                <input id="id_evolucao" type="hidden">
                <h2 class="title-encaminhamentos">Reabilitação</h2>
                <div class="row-encaminhamento">

                    <div class="position-encamin">
                        <label for="id_area" class="label-encaminhamento">Área</label>
                        <select required name="area-encaminhamento" id="areaEncamin" class="custom-select">
                            <option value="" disabled selected hidden>Selecione uma área...</option>
                            <option value="Medicina">MEDICINA</option>
                            <option value="Fisioterapia">FISIOTERAPIA</option>
                            <option value="Osteopatia">OSTEOPATIA - COLUNA</option>
                            <option value="Toc">TOC</option>
                            <option value="Nutrição">NUTRIÇÃO</option>
                            <option value="Viscosuplementação">VISCOSUPLEMENTAÇÃO</option>
                            <option value="Pilates">PILATES</option>
                            <option value="On Performance">ON PERFORMANCE</option>
                        </select>
                    </div>

                    <div class="position-encamin">
                        <label for="id_qtdSemana" class="label-encaminhamento -semana">Qtd.Semana</label>
                        <input id="qtdSemana" name="qtdSemana" class="form-control" autocomplete="off" type="text">
                    </div>

                    <div class="position-encamin">
                        <label for="id_tempo" class="label-encaminhamento -tempo">Tempo Previsto</label>
                        <input id="tempoPrevisto" name="tempoPrevisto" class="form-control" autocomplete="off" type="text">
                    </div>
                
                    <Button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px); margin-top:18px !important" onclick="adicionar_encaminhamento()" type="button">
                        <svg class="svg-inline--fa fa-plus fa-w-14 my-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""> 
                            <path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path>
                        </svg>
                    </Button>
                </div>
                <div class="table-header-scroll" style="margin-top: 1%">
                    <table>
                        <thead>
                            <tr>
                                <th width="40%" class="text-left">Área</th>
                                <th width="30%" class="text-right">Qtd.Semana</th>
                                <th width="30%" class="text-right">Tempo Previsto</th>
                                <th width="5%" class="text-right"></th>
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


                <h2 class="title-encaminhamentos" style="margin-top: 5%">Habilitação</h2>
                <div class="row-encaminhamento" style="flex-wrap:wrap; justify-content: center; height: 100%">

                    <div class="position-encamin" style="margin-right: 2%; margin-bottom: 3%">
                        <label for="teste" class="label-encaminhamento">teste</label>
                        <select required name="teste-encamin" id="testeEncamin" class="custom-select" onchange="opcoesEncaminhamento()">
                            <option value="" disabled selected hidden>Selecione uma opção...</option>
                            <option value="VO2 Específico" id="vo2Espec">VO2 ESPECÍFICO</option>
                            <option value="VO2 Basal">VO2 BASAL</option>
                            <option value="VO2 Máximo">VO2 SUB MÁXIMO</option>
                            <option value="Teste de força" id="testeF">TESTE DE FORÇA(dinanometria)</option>
                            <option value="Teste de movimento">TESTE DE MOVIMENTO(cinemática)</option>
                        </select>

                    </div>

                    <div class="position-encamin" id="esporteEncamin" style="display: none; width: 34%; margin-bottom: 3%;">
                        <div style="flex-direction: column;">
                            <label for="esporteEncamin" class="label-encaminhamento -esporte">Esporte</label>
                            <input id="esporte" name="esporte" class="form-control -esporte" autocomplete="off" type="text" style="width: 81%">
                        </div>
                    </div>

                    <div class="position-encamin" id="testeforcaCheck" style="display: none; width: 34%; margin-bottom:3%">
                        <div style="display:flex; flex-direction: column;">
                            
                            <div class="position-teste">
                                <input  id="superior" value="Superior" name="checkbox-superior" type="checkbox">
                                <label for="checkbox" class="label-encaminhamento -check">Superior</label>
                            </div>

                            <div class="position-teste">
                                <input id="inferior" value="Inferior" name="checkbox-inferior" type="checkbox">
                                <label for="checkbox" class="label-encaminhamento -check">Inferior</label>
                            </div>
                        </div>
                    </div>

                   
                    <div class="position-encamin" id="obs-encaminhamento" style="width: 34%; margin-bottom: 3%"> 
                        <label for="obs" class="label-encaminhamento"> Observação</label>
                        <input id="obs-encamin" name="obs" type="text" class="form-control" autocomplete="off">
                    </div>
                    
                
                    <Button class="btn btn-target mt-auto" id="button-add-habilit" style="height:calc(1.5em + 0.75rem + 8px); margin-top:18px !important; position: absolute; right: 64px; top: 305px;" onclick="adicionar_encaminhamento_habilitacao()" type="button">
                        <svg class="svg-inline--fa fa-plus fa-w-14 my-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""> 
                            <path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path>
                        </svg>
                    </Button> 
                

                    <div class="table-header-scroll" style="margin-top: 1%; width: 100%">
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
                    <div class="row my-3">
                        <button onclick="salvar_encaminhamento()" type="button" class="btn btn-target m-auto px-5" style="margin-top: 11% !important">Salvar</button>
                    </div>
                </div>
                
                    
            
            </div> 
        </div>  
    </div>   
</div>