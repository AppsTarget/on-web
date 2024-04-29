<!-- Modal -->
<div class="modal fade" id="alterarEmpresaModal" aria-labelledby="alterarEmpresaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="alterarEmpresaModal">Alterar Empresa</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <input id="id_plano" name="id_plano" type="hidden">
                    
                    <div class="col-md-12">
                        <label for="empresa" class="custom-label-form">Selecione a empresa *</label>
                        <select id="empresa" name="empresa" class="custom-select" autocomplete="off">
                            <option value="1">ON - EVOLUÇÃO CORPORAL</option>
                            <option value="2">ON - EVOLUCAO CORPORAL - IBIRAPUERA</option>
                        </select>
                    </div>
                    <div class='d-flex' style="justify-content: center">
                        <button onclick='alterarEmpresa();' type="button" class="btn btn-target my-3 mx-auto px-5">
                            Alterar
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