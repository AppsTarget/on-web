<!-- Modal -->
<div class="modal fade" id="videoChamadaModal" aria-labelledby="videoChamadaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="videoChamadaModalLabel">
                    Iniciar Atendimento por Vídeo Chamada?
                </h6>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="link-video-chamada" class="custom-label-form">Link para acesso do paciente</label>
                            <input id="link-video-chamada" class="form-control" autocomplete="off" type="text" readonly required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex">
                <button id="cancelar_chamada" class="btn btn-secondary mx-auto my-3 px-5">
                    Cancelar
                </button>

                <button id="iniciar_chamada" class="btn btn-target mx-auto my-3 px-5">
                    Iniciar
                </button>
            </div>
        </div>
    </div>
</div>