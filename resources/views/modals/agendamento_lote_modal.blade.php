<!-- Modal -->
<div class="modal fade" id="agendamentoLoteModal" aria-labelledby="agendamentoLoteModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form>
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color">Criar agendamento em lote - Passo 2/3</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding-bottom:0">
                    <h4 id='CRlote'class='text-center'></h4>
                    <h4 id="ASlote" class='text-center'></h4>
                    <h6 id="SEMlote" class='text-center'></h6>
                    <div class='col-12 form-group d-flex' style="margin-bottom:50px; margin-top: 15px;justify-content: center;">
                        <div class="col-4">
                            <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                            <input id="data-inicial" onchange="compararData(this)" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                        </div>
                        <div class="col-4">
                            <label for="data-final" class="custom-label-form">Data Final*</label>
                            <input id="data-final" onchange="compararData(this)" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
                        </div>
                    </div>
                </div>
                <div class="container" style="margin-bottom:30px">
                    <div class="row my-3">
                        <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="selecionarAgendamentoLote();//gerarAgendamentosEmLote();">Continuar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>