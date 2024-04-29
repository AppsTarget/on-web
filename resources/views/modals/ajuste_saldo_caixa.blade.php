<div class="modal fade" id="ajusteSaldoCaixa"  aria-labelledby="ajusteSaldoCaixa" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 580px; max-height:100%;">
        <div class="modal-content" style="display:flex; flex-direction:column;height:100%; width: 100%; position: relative;">
           <div class="modal-header">
                <h6 class="modal-title header-color header-color">Sangria/Suprimento</h6>   
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:20px 0px 15px 15px;">
                <input id="id_caixa" type="hidden">
                <h3 style="color: #000000cf">Valor Resultante (total)</h3>
                <h3>
                    <span style="color: #000000cf">
                        R$ 
                    </span>
                    <input id="valor_total" data-valor_total="" name="valor_total" type="number" step="0.01" style="border: none; color: #000000cf">
                </h3>
                <div class="row">
                    <div class="col-12" style="margin: 30px 0px 15px 0px">
                        <h4 id="title-valor-inserido-caixa">
                            Sem Alterações
                        </h4>
                    </div>
                    <div class="col-12 d-flex" style="justify-content: center; margin-bottom: 15px">
                        <div class="col-10" style="justify-content: center">
                            <label for="inserir_valor" class="custom-label-form">Valor (R$)</label>
                            <input id="inserir_valor" name="inserir_valor" onchange="control_valor_inserido_caixa($(this))" onkeyup="control_valor_inserido_caixa($(this))" onkeydown="control_valor_inserido_caixa($(this))" class="form-control text-right" placeholder="R$ 0,00" type="number" maxlength="15" autocomplete="off" style="width: 100%">
                        </div>
                    </div>
                    <div class="col-12 d-flex" style="justify-content: center; margin-bottom: 15px">
                        <div class="col-10" style="justify-content: center">
                            <label for="motivo" class="custom-label-form">Motivo</label>
                            <input id="motivo" class="form-control" name="motivo" style="width: 100%">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="id" name="id" class="btn btn-target m-auto px-5" onclick="salvar_valor_caixa()" type="button">Confirmar</button>
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div>