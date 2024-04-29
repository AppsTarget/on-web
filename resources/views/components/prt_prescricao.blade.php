<div class="container-fluid card p-3">
    <h5 class="mb-3 w-100 btn-link-target">Prescrições Anteriores</h5>
    <div class="row">
        <div id="table-prontuario-prescricao" class="accordion w-100 px-3">
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarPrescricaoModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('.modals.prt_prescricao_modal')
