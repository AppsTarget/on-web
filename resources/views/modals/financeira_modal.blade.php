<!-- Modal -->
<div class="modal fade" id="financeiraModal" aria-labelledby="financeiraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form action="/saude-beta/financeira/salvar" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="financeiraModalLabel">Cadastrar Financeira</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id="id_financeira" type="hidden">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="empresa" class="custom-label-form">Empresa *</label>
                                <select id="empresa" class="custom-select">
                                    @foreach ($empresas AS $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->descr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="tipo-baixa" class="custom-label-form">Tipo de Baixa *</label>
                                <select id="tipo-baixa" class="custom-select">
                                    <option value="D">Débito</option>
                                    <option value="C">Crédito</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="prazo" class="custom-label-form">Prazo *</label>
                                <input id="prazo" name="prazo" class="form-control" autocomplete="off" type="text" required>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="taxa-padrao" class="custom-label-form">Taxa Padrão *</label>
                                <input id="taxa-padrao" name="taxa-padrao" class="form-control money-brl" autocomplete="off" type="text" required>
                            </div>

                            {{-- <div class="col-md-12 form-group form-search">
                                <label for="pessoa_nome" class="custom-label-form">Cliente *</label>
                                <input id="pessoa_nome"
                                    name="pessoa_nome"
                                    placeholder="Digitar Nome do Cliente..."
                                    data-input="#pessoa_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="cliente"
                                    data-filter="S"
                                    class="form-control autocomplete"
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="pessoa_id" name="pessoa_id" type="hidden">
                            </div> --}}

                            


                        </div>
                        <hr>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="rede-adquirente" class="custom-label-form">Rede Adquirente *</label>
                                    <select id="rede-adquirente" class="custom-select">
                                        <option value="1">ON - Morumbi</option>
                                        <option value="2">ON - Ibirapuera</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="n-max-parcela" class="custom-label-form">Número máximo de Parcela *</label>
                                    <input id="n-max-parcela" name="n-max-parcela" class="form-control" autocomplete="off" type="text" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="taxa" class="custom-label-form">Taxa *</label>
                                    <input id="taxa" name="taxa" class="form-control money-brl text-right" autocomplete="off" type="text" required>
                                </div>
                                <div class="col-3 form-group d-grid pr-0">
                                    <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="addTaxaParcelaLista(); return false">
                                        <i class="my-icon fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3>Taxas por Parcelas</h3>
                            <div class="row" style="height:calc(100% - 60px)">
                                <div class="col-md-12 h-100 pr-0">
                                    <div class="custom-table h-100">
                                        <div class="table-header-scroll">
                                            <table id='tabela-planos'>
                                                <thead>
                                                    <tr>
                                                        <th width="70%">Rede Adquirente</th>
                                                        <th width="10%">Parcela Máxima</th>
                                                        <th width="10%" class="text-right">Taxa</th>
                                                        <th width="10%"  class="text-center"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="overflow-auto" style="height:calc(100% - 100px)">
                                            <table id="table-taxas-parcelas" class="table table-hover">
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button class="btn btn-target m-auto px-5" type="button" onclick="salvarFinanceira()">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>