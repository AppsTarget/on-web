<!-- Modal -->
<div class="modal fade" id="anamneseModal" aria-labelledby="anamneseModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="anamneseModalLabel">
                    Anamnese | -----
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="container-fluid" action="#" method="POST" onsubmit="salvar_anamnese(event)">
                    <input id="id_anamnese" type="hidden">

                    <div id="questionario-anamnese">
                    </div>

                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
