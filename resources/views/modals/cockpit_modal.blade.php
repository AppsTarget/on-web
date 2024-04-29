<!-- Modal -->
<div class="modal fade" id="cockpitModal" aria-labelledby="cockpitModalLabel" aria-modal="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 99%;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="cockpitModalLabel">
                    COCKPIT
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-body-IEC" class="modal-body">
                <input id="value" type="hidden">
                <div id="loading-modal-cockpit" style="width: 0%;height: 1px;background: #0067d5;transition: 1s width;"></div>
                <div class="row">
                    <div class="d-flex col-12" style="justify-content: space-between;height: 50px;padding: 15px 15px 0px 15px;">
                        <h3 id="titulo-cockpit-modal"></h3>
                        <div id="filtro-faturamento">
                            <input style="width: 100px;position: relative;left: 61px;height:24px" 
                                name="visualizacao-cockpit"
                                id="visualizacao-cockpit"
                                class="checkbox custom-control-input" type="checkbox"
                                onchange="controlVisualizacaoFaturamentoCockpit($(this))">
                            <label for="visualizacao-cockpit" class="custom-control-label" style="position: relative;top: 10px;line-height: 25px;">Aplicar visualização secundária?<label>
                        </div>
                    </div>
                    <div id="table-cockpit" class="accordion w-100 px-3">
                        
                    </div>
                    {{--
                    <div class="d-flex col-12" style="justify-content: end">
                        <button id="botao-impressao-cockpit" type="button" data-dados="" onclick="imprimir_cockpit($(this))">
                            <div style="width: 30px;margin: 2px 10px 2px 10px;">
                                <img class="custom-image" src="{{ asset("img/ferramenta-de-impressao.png") }}">
                            </div>
                        </button>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>
</div>