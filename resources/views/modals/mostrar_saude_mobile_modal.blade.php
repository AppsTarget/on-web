<!-- Modal -->
<div class="modal -mobile fade" id="mostrarSaudeMobileModal" aria-labelledby="mostrarSaudeMobileModalModalLabel" aria-hidden="true">
    <div class="modal-dialog -mobile modal-lg" role="document" style="max-width:94%">
        <div class="modal-content -mobile">
            <div class="container-fluid -mobile card -mobile p-3 -mobile">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 25px;">
                    <span aria-hidden="true" style="font-size:2rem">×</span>
                </button>
                <div style="width: 85%;margin-top: 35px;margin-left: 15px;">
                    <div>
                        <div class="d-flex" style="opacity: .9; width: 90%">
                            <h2 id="titulo" style="color: #161313;">
                                
                            </h2>
                            <img id="titulo-img" style = "width: 40px;height: 40px;margin-left: 15px;margin-top: -8px;" src="">
                        </div>
                        {{-- <div style="width: 97%;height: 1px;background: black;margin-top: -8px;opacity: .7;"></div> --}}
                        <div>
                            <p id="descricao" style="color: #2b2b2b;margin-top: 5px;line-height: 1.2;">
                                
                            </p>
                        </div>
                    </div>
                    <div>
                        <h4 class="titleCharts" style="color: #2b2b2b;margin-top: 20px;font-size: 20px;">
                            Hoje
                        </h4>
                    </div>
                    <div id="div-grafico-3" style="max-width: 90%">
                        <canvas id="grafico1" width="400" height="300"></canvas>
                    </div>
                    <div>
                        <h4 class="titleCharts" style="color: #2b2b2b;margin-top: 20px;font-size: 20px;">
                            Últimos 7 dias
                        </h4>
                    </div>
                    <div id="div-grafico-4" style="max-width: 90%">
                        <canvas id="grafico2" width="400" height="300"></canvas>
                    </div>
                    <div style="height: 100px"></div>
                </div>
            </div>
        </div>
    </div>
</div>











