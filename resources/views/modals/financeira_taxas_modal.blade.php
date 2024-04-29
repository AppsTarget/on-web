<!-- Modal -->
<div class="modal fade" id="financeiraTaxasModal" aria-labelledby="financeiraTaxasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="financeiraTaxasModalLabel">Configurar Taxas da Financeira</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        @csrf
                        <input id="id_financeira" name="id_financeira" type="hidden">

                        <div class="col-md-4 form-group">
                            <label for="num_min" class="custom-label-form">Mín. Parcela</label>
                            <input id="num_min" name="num_min" class="form-control money" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="num_max" class="custom-label-form">Máx. Parcela</label>
                            <input id="num_max" name="num_max" class="form-control money" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="taxa" class="custom-label-form">Taxa(%)</label>
                            <input id="taxa" name="taxa" class="form-control money" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-1 form-group d-grid">
                            <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_financeira_taxas()">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div>

                        <div class="col-md-12">
                            <div class="custom-table card h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="35%" class="text-center">Mínimo de Parcelas</th>
                                                <th width="35%" class="text-center">Máximo de Parcelas</th>
                                                <th width="20%" class="text-center">Taxa(%)</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-body-scroll">
                                    <table id="table-financeira-taxas" class="table table-hover">
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