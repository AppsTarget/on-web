<!-- Modal -->
<div class="modal fade" id="supervisorModal" aria-labelledby="supervisorModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="supervisorModalLabel">
                    Supervisor necessário
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id = "supervisorModalForm"
                      class = "container-fluid"
                >
                    @csrf
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="emailsup" class = "custom-label-form">E-mail:</label>
                            <input id = "emailsup" class = "form-control" autocomplete = "off" type = "text" />
                        </div>
                        <div class="col-6 form-group">
                            <label for="passwordsup" class = "custom-label-form">Senha:</label>
                            <input id = "passwordsup" class = "form-control" autocomplete = "off" type = "password" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="motivosup" class = "custom-label-form">Motivo da alteração de valor:</label>
                            <textarea id = "motivosup" class = "form-control"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type="button" onclick = "validarSupervisor()">Ok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>