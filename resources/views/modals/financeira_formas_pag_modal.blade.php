<!-- Modal -->
<div class="modal fade" id="financeiraFormasPagModal" aria-labelledby="financeiraFormasPagModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="financeiraFormasPagModalLabel">Definir Financeiras na Forma de Pagamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        @csrf
                        <input id="id_forma_pag" name="id_forma_pag" type="hidden">

                        <div class="col-md-10 form-group form-search">
                            <label for="financeira_nome" class="custom-label-form">Financeira</label>   
                            <input id="financeira_nome"
                                name="financeira_nome"  
                                class="form-control autocomplete" 
                                placeholder="Digitar Nome da Financeira..."
                                data-input="#financeira_id"
                                data-table="financeira" 
                                data-column="descr" 
                                data-filter_col="id_emp"
                                data-filter="{{ getEmpresa() }}"
                                type="text" 
                                autocomplete="off"
                                required>
                            <input id="financeira_id" name="financeira_id" type="hidden">
                        </div>

                        <div class="col-md-1 form-group d-grid">
                            <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_financeira_formas_pag()">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div>

                        <div class="col-md-12">
                            <div class="custom-table card h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="90%">Financeira</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-body-scroll">
                                    <table id="table-financeira-formas-pag" class="table table-hover">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>