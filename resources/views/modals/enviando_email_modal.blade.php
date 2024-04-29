<!-- Modal -->
<div class="modal fade" id="enviandoEmailModal" aria-labelledby="enviandoEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="enviandoEmailModalLabel">Enviar E-mail de Recuperação</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div id="loading-email" style="opacity: 0.8;display: flex;justify-content: center;width: 100%;height: 500%;margin-top: -40px;">
                        <div>
                            <div>
                                <img src="http://vps.targetclient.com.br/saude-beta/img/logo_topo_limpo_on.png">
                            </div>
                            <div class='d-flex' style='justify-content: center;margin-bottom: 35px;'>
                                <div class="loader"></div>
                            </div>
                        </div>
                    </div>
                    <div id="msg-sucess" style="display: none">
                        <h3>Email Enviado com sucesso</h3>
                        <p>Foi enviado um e-mail contendo o link de criação/alteração de senha para <span id="email">vinicavani@hotmail.com</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
</style>