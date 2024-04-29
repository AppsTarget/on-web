<div class="custom-table card">
    <div class="d-flex" style="justify-content: end">
        <div class='col-3' style="margin: 5px 0px 5px 0px;">
            <select id="select-contratos" class='custom-select' onChange="pedidos_por_pessoa($('#id_pessoa_prontuario').val())">
                <option value="0">Sistema atual</option>
                <option value="1">Sistema antigo</option>
            </select>
        </div>
    </div>
    <div class="table-header-scroll">
        <table>
            <thead>
                <tr>
                    <th width="10%" class="text-center">Atividades</th>
                    <th width="10%">Contratação</th>
                    <th width="20%" class="text-center">Empresa</th>
                    <th width="10%" class="text-right">Valor</th>
                    <th width="10%" class="text-center">Válido Até</th>
                    <th width="10%" class="text-center">Status</th>
                    <th width="20%" class="text-center">Ações</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="table-body-scroll custom-scrollbar">
        <table id="table-prontuario-pedido" class="table table-hover">
            <tbody>
            </tbody>
        </table>
    </div>
</div>
{{-- 
<button class="btn btn-primary custom-fab" type="button" onclick="abrir_pedido()">
    <i class="my-icon fas fa-plus"></i>
</button> --}}

@include('modals.pedido_modal')
@include('modals.pedido_evolucao_modal')

@include('modals.pedido_modal')
@include('modals.completar_cadastro_modal')
@include('modals.congelar_pedido_modal')
@include('modals.pedido_pessoas_modal')