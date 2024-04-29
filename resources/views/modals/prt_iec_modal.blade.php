<!-- Modal -->
<div class="modal fade" id="iecModal" aria-labelledby="iecModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="iecModalLabel">
                    IEC | -----
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="container-fluid" action="#" method="POST" onsubmit="salvar_iec(event)">
                    <input id="id_iec" type="hidden">

                    <div id="questionario-iec">
                    </div>

                    <div id="obs1" class="obs">
                        <textarea name="obs" id="obs" cols="80" rows="5" maxlength="400" style = "width:100%" placeholder="Digite uma observação."></textarea>
                    </div>

                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
