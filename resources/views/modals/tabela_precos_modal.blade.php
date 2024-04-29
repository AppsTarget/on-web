<!-- Modal -->
<div class="modal fade" id="tabelaPrecosModal" aria-labelledby="tabelaPrecosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="tabelaPrecosModalLabel">Cadastrar Planos</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/tabela-precos/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="col-md-8 form-group">
                            <label for="descr" class="custom-label-form">Descrição *</label>
                            <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="status" class="custom-label-form">Status</label>
                            <select id="status" name="status" class="form-control custom-select">
                                <option value="A">Ativo</option>
                                <option value="I">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="vigencia" class="custom-label-form">Vigência</label>
                            <select id="vigencia" name="vigencia" class="form-control custom-select">
                                <option value="M">Mensal</option>
                                <option value="B">Bimestral</option>
                                <option value="T">Trimestral</option>
                                <option value="S">Semestral</option>
                                <option value="A">Anual</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="max_atv_semana" class="custom-label-form">Máx. atv. p/ semana</label>
                            <input id="max_atv_semana" name="max_atv_semana" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="max_atv" class="custom-label-form">Máx. atividades</label>
                            <input id="max_atv" name="max_atv" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor" class="custom-label-form">Valor (R$)</label>
                            <input id="valor" name="valor" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="npessoas" class="custom-label-form">Nº Pessoas</label>
                            <input id="npessoas" name="npessoas" class="form-control " autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="desc_associado" class="custom-label-form" style="white-space: nowrap;">Desc. Associados (R$)</label>
                            <input type='number' id="desc_associado" name="desc_associado" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="descr_contrato" class="custom-label-form">Descrição Contrato *</label>
                            <textarea id="descr_contrato" name="descr_contrato" class="form-control" autocomplete="off" type="text" required></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="tipo_agendamento" class="custom-label-form" style="white-space: nowrap;">Tipo</label>
                            <select id="tipo_agendamento" name="tipo_agendamento" class="custom-select">
                                <option value='1'>Pré-agendamento</option>
                                <option value='2'>Reabilitação</option>
                                <option value='3'>Habilitação</option>
                            </select>
                        </div>
                        
                        <div id="lista-empresa" class="col-10" style="max-width:88%; flex: 0 0 88%">
                            <div class="row">
                                <div class="col-md-10 form-group">
                                    <label for="empresa" class="custom-label-form">Empresas *</label>
                                    <select id="empresa" name="empresa[]" class="form-control custom-select">
                                        <option value="0">Selecionar Empresa...</option>
                                        @foreach ($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">
                                            {{ $empresa->descr }}   
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 form-group d-flex">
                                    <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa2($(this)); return false;">
                                        <i class="my-icon fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa2($(this)); return false;">
                                        <i class="my-icon fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px; top:-5px;'>
                            <input id="repor_som_mes" name="repor_som_mes" class="custom-control-input" type="checkbox">
                            <label for="repor_som_mes" class="custom-control-label">
                                <span style='color: #6c6c6c;'>Repor atividades somente dentro do mês?</span>
                            </label>
                        </div>
                        <div class="col-md-9 custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;
                        top: -5px;'>
                            <input id="usar_desconto_padrao" name="usar_desconto_padrao" class="custom-control-input" type="checkbox">
                            <label for="usar_desconto_padrao" class="custom-control-label">
                                <span style='color: #6c6c6c;'>Usar desconto geral?</span>
                            </label>
                        </div>
                        <div class="col-md-9 custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;
                        top: -5px;'>
                            <input id="gerar_contrato" name="gerar_contrato" class="custom-control-input" type="checkbox">
                            <label for="gerar_contrato" class="custom-control-label">
                                <span style='color: #6c6c6c;'>Usar contratos assinados digitalmente?</span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-target my-3 mx-auto px-5" onclick="salvar_tabela_precos1();">
                            Gravar
                        </button>
                    </form>

                    <div style="display:flex" id="procedimento-agenda">
                        <div class="col-md-5 form-group form-search" style="max-width: 100%;min-width: 66%;">
                            <label for="procedimento-nome-agenda" class="custom-label-form">Modalidade *</label>
                            <input id="procedimento-nome-agenda"
                                name="procedimento_nome-agenda"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome do procedimento..."
                                data-input="#procedimento-id"
                                data-table="procedimento"
                                data-column="descr"
                                data-filter_col="id_emp"
                                data-filter="{{ getEmpresa() }}"
                                type="text"
                                autocomplete="off"
                                novalidate>
                            <input id="procedimento-id" name="procedimento_id" type="hidden">
                        </div>
                        <div class="col-4 form-group d-grid">
                            <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_modalidade()" type="button">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div> 
                    </div>
                    <div class="col-md-12">
                        <div class="custom-table card h-100">
                            <div class="table-header-scroll">
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="40%">Modalidade</th>
                                            <th width="20%">Área da saúde</th>
                                            <th width='5%'></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="table-body-scroll">
                                <table id="table-modalidades" class="table table-hover">
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