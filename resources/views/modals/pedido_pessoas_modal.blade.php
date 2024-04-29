<!-- Modal -->
<div class="modal fade" id="pedidoPessoasModal" aria-labelledby="pedidoPessoasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="pedidoPessoasModalLabel">Adicionar Pessoas</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/tabela-precos/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">
                        <input id="id_linha" name='id_linha' type='hidden'>
                        <input id="total_pessoas" name="total_pessoas" type="hidden">

                    <div style="display:flex" id="procedimento-agenda">
                        <div class="col-12 form-search">
                            <label for="paciente_nome" class="custom-label-form">associado *</label>   
                            <input id="paciente_nome"
                                name="paciente_nome"  
                                class="form-control autocomplete" 
                                placeholder="Digitar Nome do associado..."
                                data-input="#paciente_id"
                                data-table="pessoa" 
                                data-column="nome_fantasia" 
                                data-filter_col="paciente"
                                data-filter="S"
                                type="text" 
                                autocomplete="off"
                                required>
                            <input id="paciente_id" name="paciente_id" type="hidden">
                        </div>
                        <div class="col-4 form-group d-grid" style="margin-bottom: 0;">
                            <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pessoa_pedido_plano()" type="button">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div> 
                    </div>
                        <div class="col-md-12" style="top: 15px;">
                            <div class="custom-table card h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="80%">Nome</th>
                                                <th width='20%'></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-body-scroll">
                                    <table id="table-pessoas-pedido" class="table table-hover" style="margin-bottom: 0px">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group d-grid" style="position: relative;top: 25px;left: 116px;">
                            <button id='adicionar-pessoas-plano'class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="gravarIdsPessoas()" type="button" disabled>
                                Confirmar
                            </button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>