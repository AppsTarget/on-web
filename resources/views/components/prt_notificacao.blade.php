<div class="container-fluid card p-3">
    <h5 class="w-100 mb-3 btn-link-target">Histórico de Notificações</h5>
    <div class="row">
        <div id="table-prontuario-notificacao-pessoa" class="accordion w-100 px-3">
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarNotificacaoModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include ('.modals.criar_notificacao_modal')