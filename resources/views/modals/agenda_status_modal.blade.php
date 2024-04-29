
<!-- Modal -->
<div class="modal fade" id="agendaStatusModal" aria-labelledby="agendaStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="agendaStatusModalLabel">Cadastrar Status da Agendas</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/saude-beta/agenda-status/salvar" method="POST">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            @csrf
                            <input id="id" name="id" type="hidden">
                            <div class="col-8">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-2 px-1">
                                <label for="cor" class="custom-label-form">Fundo *</label>
                                <input id="cor" name="cor" type="hidden" value="#78909C" required>
                                <div class="colorpalette" data-input_id="#cor"></div>
                            </div>
                            <div class="col-2 px-1">
                                <label for="cor_letra" class="custom-label-form">Texto *</label>
                                <input id="cor_letra" name="cor_letra" type="hidden" value="#78909C" required>
                                <div class="colorpalette" data-input_id="#cor_letra"></div>
                            </div>
                            <div class="col-12">
                                <h5 class="custom-label-form mt-3">Permissões:</h5>
                            </div>
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input id="permite_editar" name="permite_editar" class="custom-control-input permissoes" type="checkbox">
                                    <label for="permite_editar" class="custom-control-label" style="width:120px">Permite editar </label>
                                    <img id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -24px;top: -2px;" onclick="informarStatusAgenda('trava')" src="/saude-beta/img/icone-de-informacao.png">
                                </div> 
                            </div>
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input id="caso_confirmar" name="caso_confirmar" class="custom-control-input" type="checkbox">
                                    <label for="caso_confirmar" class="custom-control-label">Definir como confirmado</label>
                                    <img id="travar-escolha-ag-status"class="ico-info" style="position: relative;top: -2px;left: 1px;"  src="/saude-beta/img/icone-de-informacao.png">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input id="permite_reagendar" name="permite_reagendar" class="custom-control-input permissoes" type="checkbox">
                                    <label for="permite_reagendar" class="custom-control-label">Permite reagendar</label>
                                    <img id="travar-escolha-ag-status"class="ico-info" style="position: relative;top: -2px;left: 0px;" src="/saude-beta/img/icone-de-informacao.png">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input id="caso_reagendar" name="caso_reagendar" class="custom-control-input permissoes" type="checkbox">
                                    <label for="caso_reagendar" class="custom-control-label" style="width:175px">Definir como reagendado</label>
                                    <img id="caso-reagendar-ag-status"class="ico-info" style="position: relative;left: -10px;top: -3px;" onclick="informarStatusAgenda('reagendado')"src="/saude-beta/img/icone-de-informacao.png">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="custom-control custom-switch">
                                    <input id="caso_cancelar" name="caso_cancelar" class="custom-control-input" type="checkbox">
                                    <label for="caso_cancelar" class="custom-control-label" style="width:175px">Definir como cancelado</label>
                                    <img id="travar-escolha-ag-status"class="ico-info" style="position: relative;top: -3px;left: -21px;" src="/saude-beta/img/icone-de-informacao.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-target mx-auto my-3 px-5">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
