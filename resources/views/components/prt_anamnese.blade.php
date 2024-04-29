<div class="container-fluid card p-3">
    <h5 class="w-100 mb-3 btn-link-target">Anamneses</h5>
    <div class="row">
        <div id="table-prontuario-anamnese-pessoa" class="accordion w-100 px-3">
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#selecaoAnamneseModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('.modals.prt_selecao_anamnese_modal')
@include('.modals.prt_anamnese_modal')
@include('.modals.prt_visualizar_anamnese_modal')
