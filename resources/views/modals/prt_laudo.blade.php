<!-- Modal -->
<div class="modal fade" id="laudoIECModal" aria-labelledby="selecaoAnamneseModalLabel" aria-modal="true">
    <div class="modal-dialog" role="document" style = "max-width:650px">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color">
                    Laudo
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style = "text-align:center">
                <div class="container-fluid card" id = "chart-content-tudo">
                </div>
                <form method = "post" action = "/saude-beta/IEC/laudo">
                    @csrf
                    <input type = "hidden" id = "laudo_idPessoa" name = "id_pessoa" />
                    <input type = "hidden" id = "laudo_grafico" name = "grafico"/>
                    <input type = "hidden" id = "laudo_endereco" name = "endereco" />
                    <textarea id = "laudo_diagnostico" name = "diagnostico" placeholder = "Observações" style = "
                        width:100%;
                        margin-top:20px;
                        text-transform:none !important;
                        border: 1px solid rgba(0,0,0,.125);
                        border-radius: 0.25rem;
                        padding:10px;
                        position:relative;
                        z-index:800"
                    ></textarea>
                    <button id = "laudoBtnModal" class="btn btn-target m-auto px-5" style="margin-top:20px !important" type="submit">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
