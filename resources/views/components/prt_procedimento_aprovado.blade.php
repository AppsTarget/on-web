<div>
    <div class="input-group mb-3">
        <input id="pesquisa-procedimentos-aprovados" class="form-control" placeholder="Pesquisar..." type="text" aria-label="Recipient's username" aria-describedby="basic-addon2" onkeypress="javascript: if(event.keyCode == 13) pesquisar_procedimentos_aprovados();">
        <div class="input-group-append">
            <button class="btn btn-form" type="button" onclick="pesquisar_procedimentos_aprovados()">
                <i class="my-icon fal fa-search"></i>
            </button>
        </div>
    </div>

    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr>
                        <th width="10%" class="text-center">Nº Plano</th>
                        <th width="20%">Associado</th>
                        <th width="15%">Responsável</th>
                        {{-- <th width="8%" class="text-right">Dente</th>
                        <th width="8%" class="text-right">Face</th> --}}
                        <th width="20%">
                        <th width="10%" class="text-center">Status</th>
                        <th width="10%" class="text-right">Valor</th>
                        {{-- <th width="8%" class="text-right">Valor</th> --}}
                        <th width="10%" class="text-right">Data de encerramento</th>
                        {{-- <th width="12%" class="text-center"></th> --}}
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-prontuario-procedimentos-aprovados" class="table table-hover">
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- @include('modals.evolucao_pedido_modal')
@include('modals.finalizar_pedido_servicos_modal') --}}
