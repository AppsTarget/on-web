<div class="container-fluid card p-3">
    <h5 class="w-100 mb-3 btn-link-target">Receitas</h5>
    <div class="row">
        <div class="w-100">
            <div id="table-prontuario-receita" class="accordion w-100 px-3">
            </div>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarReceitaModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('.modals.prt_receita_modal')
