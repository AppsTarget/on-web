<!-- Modal -->
<div class="modal fade" id="adicionarPlanoDeContasModal" aria-labelledby="adicionarPlanoDeContasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="adicionarPlanoDeContasModal">Plano de Contas</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <input id="id"       type="hidden">
                    <input id="id-pai"   type="hidden">
                    <div class="col-md-12">
                        <label for="descr-pai" class="custom-label-form">Descrição Pai *</label>
                        <input id="descr-pai" name="descr-pai" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label for="descr-filho" class="custom-label-form">Descrição Filho *</label>
                        <input id="descr-filho" name="descr-filho" class="form-control">
                    </div>
                    <div class='d-flex' style="justify-content: center">
                        <button onclick='salvarPlanoDeContas();' type="button" class="btn btn-target my-3 mx-auto px-5">
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function alterarEmpresa() {
        $.get(
            '/saude-beta/pessoa/alterar-empresa/', {
                _token: $('meta[name=csrf-token]').attr('content'),
                id_emp: $('#alterarEmpresaModal #empresa').val()
            },
            function(data,status) {
                console.log(data + ' | ' + status)
                location.reload(true)
            }
        )
    }
</script>