<div class="modal -mobile fade" id="criarVitruviano" aria-labelledby="criarVitruvianoModalLabel" aria-hidden="true">
    <div class="modal-dialog -mobile modal-lg" role="document" style="max-width:94%">
        <div class="modal-content -mobile">
            <div class="container-fluid -mobile card -mobile p-3 -mobile">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 25px;">
                    <span aria-hidden="true" style="font-size:2rem">×</span>
                </button>
                <h5 class="w-100 mb-3 btn-link-target">Visualizar Evoluções</h5>    
                <div class="row -mobile">
                    <div id="table-prontuario-vitruviano" class="accordion w-100 px-3" style="display: flex;align-content: center;justify-content: center; height:511px;">
                        <div style="position: relative;height: 550px;">
                            <div>
                                <img class="img-mobile"style="min-width: 505px"src="/saude-beta/img/vitruvian_circle.png" alt="Vitruviano">
                            </div>
                            <div class="partes -mobile" style="height: 511px;top: -512px;position: relative;">
                                <div id='mao-direita-mov' onclick="resumo_vitruviano_modal(1)" onmouseover="resumo_vitruviano(2,1)" class='regiao-vitruviano'></div>
                                <div id='mao-direita-pa' onclick="resumo_vitruviano_modal(2)" onmouseover="resumo_vitruviano(2,2)" class='regiao-vitruviano '></div>
                                <div id='mao-esquerda-pa' onclick="resumo_vitruviano_modal(3)" onmouseover="resumo_vitruviano(2,3)" class='regiao-vitruviano '></div>
                                <div id='mao-esquerda-mov' onclick="resumo_vitruviano_modal(4)" onmouseover="resumo_vitruviano(2,4)" class='regiao-vitruviano '></div>

                                <div id='cotovelo-direito-mov'  onclick="resumo_vitruviano_modal(5)" onmouseover="resumo_vitruviano(2,5)" class='regiao-vitruviano '></div>
                                <div id='cotovelo-direito-pa' onclick="resumo_vitruviano_modal(6)" onmouseover="resumo_vitruviano(2,6)" class='regiao-vitruviano '></div>
                                <div id='cotovelo-esquerdo-mov' onclick="resumo_vitruviano_modal(7)" onmouseover="resumo_vitruviano(2,7)" class='regiao-vitruviano '></div>
                                <div id='cotovelo-esquerdo-pa' onclick="resumo_vitruviano_modal(8)" onmouseover="resumo_vitruviano(2,8)" class='regiao-vitruviano '></div>

                                <div id='joelho-direito-mov'  onclick="resumo_vitruviano_modal(9)" onmouseover="resumo_vitruviano(2,9)" class='regiao-vitruviano '></div>
                                <div id='joelho-direito-pa' onclick="resumo_vitruviano_modal(10)" onmouseover="resumo_vitruviano(2,10)" class='regiao-vitruviano '></div>
                                <div id='joelho-esquerdo-mov' onclick="resumo_vitruviano_modal(11)" onmouseover="resumo_vitruviano(2,11)" class='regiao-vitruviano '></div>
                                <div id='joelho-esquerdo-pa' onclick="resumo_vitruviano_modal(12)" onmouseover="resumo_vitruviano(2,12)" class='regiao-vitruviano '></div>

                                <div id='pe-direito-mov'  onclick="resumo_vitruviano_modal(13)" onmouseover="resumo_vitruviano(2,13)" class='regiao-vitruviano '></div>
                                <div id='pe-direito-pa' onclick="resumo_vitruviano_modal(14)" onmouseover="resumo_vitruviano(2,14)" class='regiao-vitruviano '></div>
                                <div id='pe-esquerdo-mov' onclick="resumo_vitruviano_modal(15)" onmouseover="resumo_vitruviano(2,15)" class='regiao-vitruviano '></div>
                                <div id='pe-esquerdo-pa' onclick="resumo_vitruviano_modal(16)" onmouseover="resumo_vitruviano(2,16)" class='regiao-vitruviano '></div>

                                <div id='cardio-pulmonar'  onclick="resumo_vitruviano_modal(17)" onmouseover="resumo_vitruviano(2,17)" class='regiao-vitruviano'></div>
                                <div id='coluna' onclick="resumo_vitruviano_modal(18)" onmouseover="resumo_vitruviano(2,18)" class='regiao-vitruviano '></div>
                                <div id='abdomen' onclick="resumo_vitruviano_modal(19)" onmouseover="resumo_vitruviano(2,19)" class='regiao-vitruviano '></div>
                                <div id='quadril' onclick="resumo_vitruviano_modal(20)" onmouseover="resumo_vitruviano(2,20)" class='regiao-vitruviano '></div>

                                <div id='ombro-direito' onclick="resumo_vitruviano_modal(21)" onmouseover="resumo_vitruviano(2,21)" class='regiao-vitruviano'></div>
                                <div id='ombro-esquerdo' onclick="resumo_vitruviano_modal(22)" onmouseover="resumo_vitruviano(2,22)" class='regiao-vitruviano'></div>
                                <div id='cabeca' onclick="resumo_vitruviano_modal(23)" onmouseover="resumo_vitruviano(2,23)" class='regiao-vitruviano'></div>
                            

                                <div id="info-corpo">
                                    <div id="info-header">
                                        <h2 id='info-title' style="font-size: 29px;"> Cotovelo direito em movimento</h2>
                                        <p>Sem Registros<p>
                                    </div>
                                    <ul id="info-vitruviano" class="timeline"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>     
            </div>
        </div>
    </div>
</div>