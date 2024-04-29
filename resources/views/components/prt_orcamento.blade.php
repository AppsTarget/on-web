<div class="custom-table card">
    <div class="table-header-scroll">
        <table>
            <thead>
                <tr>
                    <th width="8%" class="text-center">Código</th>
                    <th width="10%">Membro</th>
                    <th width="13%">Convênio</th>
                    <th width="10%" class="text-right">A Vista</th>
                    <th width="10%" class="text-right">A Prazo</th>
                    <th width="10%">Válido Até</th>
                    <th width="15%" class="text-center">Status</th>
                    <th width="20%" class="text-center">Ações</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="table-body-scroll msg-if-empty custom-scrollbar">
        <table id="table-prontuario-orcamento" class="table table-hover">
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="abrir_orcamento()">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('modals.orcamento_modal')
@include('modals.orcamento_conversao_modal')
