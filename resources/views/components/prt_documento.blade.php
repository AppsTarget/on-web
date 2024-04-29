<div class="container-fluid card p-3">
    <h5 class="w-100 btn-link-target">Documentos</h5>
    <div class="d-flex" style="justify-content: end; margin-top: -40px">
        <div class="col-4" style="margin: 5px -13px 5px 0px;">
            <select id="select-pasta-doc" class="custom-select" onchange="documentos_por_pessoa($('#id_pessoa_prontuario').val())">
                <option value="0">Todos</option>
                <option value="1">Fisioterapia e osteopatia</option>
                <option value="2">Receituario e relatórios</option>
                <option value="3">Pedidos de exames preventivos</option>
                <option value="4">Laboratórios</option>
                <option value="5">Pedidos de exames de imagem</option>
                <option value="6">Programa semanal ON - periodização</option>
                <option value="7">ON life track</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="w-100">
            <div id="table-prontuario-documento" class="accordion w-100 px-3">
            </div>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarDocumentoModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('.modals.prt_documento_modal')
