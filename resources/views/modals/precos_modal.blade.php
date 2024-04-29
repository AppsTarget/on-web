<!-- Modal -->
<div class="modal fade" id="precosModal" aria-labelledby="precosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="precosModalLabel">Cadastrar Metas Por Plano</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        @csrf
                        <input id="id-tabela-preco" name="id_tabela_preco" type="hidden">

                        <div class="col-12">
                            <h4 class="m-0">Metas</h4>
                        </div>

                        <div class="col-md-5 form-group">
                            <label for="modalidade-id" class="custom-label-form">Modalidade *</label>
                            <select id="modalidade-id"name="procedimento_nome"class="form-control autocomplete"required>
                                <option value="0">Selecionar Modalidade</option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="de" class="custom-label-form">A partir de:</label>
                            <input id="de" name="de" class="form-control" autocomplete="off" type="text" required value='0' placeholder="Dias">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="ate" class="custom-label-form">Até:</label>
                            <input id="ate" name="ate" class="form-control" autocomplete="off" type="text" value='0'>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="valor2" class="custom-label-form">Valor (R$):</label>
                            <input id="valor2" name="valor2" class="form-control" autocomplete="off" type="text" value='0'>
                        </div>

                        <div class="col-1 form-group d-grid">
                            <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_preco()">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div>

                        {{-- <div class="col-12">
                            <h4 class="m-0">Pesquisa</h4>
                        </div>

                        <div id="filtro-tabela-precos" class="row w-100 m-0">
                            <div class="col-md-8 form-group form-search">
                                <label for="filtro-procedimento" class="custom-label-form">Modalidade</label>
                                <input id="filtro-procedimento" class="form-control" autocomplete="off" type="text" required>
                            </div>

                            <div class="col-md-3 form-group">
                                <label for="filtro-especialidade" class="custom-label-form">especialidade</label>
                                <select id="filtro-especialidade" class="custom-select">
                                    <option value="0">Todas</option>
                                    @foreach ($especialidades as $especialidade)
                                        <option value="{{ $especialidade->id }}">
                                            {{ $especialidade->descr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="filtrar_tabela_precos()">
                                    <i class="my-icon fas fa-search"></i>
                                </button>
                            </div>
                        </div> --}}

                        <div class="col-md-12">
                            <div class="custom-table card h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="40%">Modalidade</th>
                                                <th width="20%">Área da saúde</th>
                                                <th width="10%" class="text-right">Min</th>
                                                <th width="10%" class="text-right">Máx</th>
                                                <th width="10%" class="text-right">Valor</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-body-scroll">
                                    <table id="table-precos" class="table table-hover">
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
