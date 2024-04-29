<!-- Modal -->
<div class="modal fade" id="tabelaPrecosModal2" aria-labelledby="tabelaPrecosModal2Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="tabelaPrecosModal2Label">Cadastrar Planos</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/tabela-precos/salvar" method="POST">
                        @csrf
                        <input id="id2" name="id" type="hidden">

                        <div class="col-md-8 form-group">
                            <label for="descr2" class="custom-label-form">Descrição *</label>
                            <input id="descr2" name="descr2" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        
                        <div class="col-md-4 form-group">
                            <label for="vigencia2" class="custom-label-form">Vigência</label>
                            <select id="vigencia2" name="vigencia2" class="form-control custom-select">
                                <option value="M">Mensal</option>
                                <option value="B">Bimestral</option>
                                <option value="T">Trimestral</option>
                                <option value="S">Semestral</option>
                                <option value="A">Anual</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="max_atv_semana2" class="custom-label-form">Máx. atv. p/ semana</label>
                            <input id="max_atv_semana2" name="max_atv_semana2" class="form-control " autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="max_atv2" class="custom-label-form">Máx. atividades</label>
                            <input id="max_atv2" name="max_atv2" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor2" class="custom-label-form">Valor (R$)</label>
                            <input id="valor2" name="valor2" class="form-control " autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="npessoas2" class="custom-label-form">Nº Pessoas</label>
                            <input id="npessoas2" name="npessoas" class="form-control " autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="status2" class="custom-label-form">Status</label>
                            <select id="status2" name="status2" class="form-control custom-select">
                                <option value="A">Ativo</option>
                                <option value="I">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="desc_associado" class="custom-label-form" style="white-space: nowrap;">Desc. Associados (R$)</label>
                            <input type='number' id="desc_associado" name="desc_associado" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="tipo_agendamento2" class="custom-label-form" style="white-space: nowrap;">Desc. Associados (R$)</label>
                            <select id="tipo_agendamento2" name="tipo_agendamento2" class="custom-select">
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
                                    <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">
                                        <i class="my-icon fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">
                                        <i class="my-icon fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;
                        top: -5px;'>
                            <input id="repor_som_mes2" name="repor_som_mes2" class="custom-control-input" type="checkbox">
                            <label for="repor_som_mes2" class="custom-control-label">
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
                        
                        <button type="button" onclick="criar_plano();" class="btn btn-target my-3 mx-auto px-5">
                            Criar novo plano
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>