<!-- Modal -->
<div class="modal fade" id="convenioModal" aria-labelledby="convenioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="convenioModalLabel">Cadastrar Convênio</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/convenio/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="col-md-8 form-group">
                            <label for="descr" class="custom-label-form">Descrição *</label>
                            <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="prazo" class="custom-label-form">Prazo *</label>
                            <input id="prazo" name="prazo" class="form-control" autocomplete="off" type="number" required>
                        </div>
                        <div class="col-md-12 form-group form-search">
                            <div class="custom-control custom-checkbox mt-2 ml-1">
                                <input id="quem-paga" name="quem_paga" class="custom-control-input" type="checkbox">
                                <label for="quem-paga" class="custom-control-label">Cliente Paga?</label>
                            </div>
                            {{-- <label for="cliente_nome" class="custom-label-form">Cliente *</label> --}}
                            <input id="cliente_nome"
                                name="cliente_nome"
                                class="form-control autocomplete"
                                placeholder="Digitar Nome da Pessoa Jurídica..."
                                data-input="#cliente_id"
                                data-table="pessoa"
                                data-column="nome_fantasia"
                                data-filter_col="tpessoa"
                                data-filter="j"
                                type="text"
                                autocomplete="off"
                                required>
                            <input id="cliente_id" name="cliente_id" type="hidden">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="plano" class="custom-label-form">Plano *</label>
                            <select id="plano" name="plano" class="custom-select"required>
                                @foreach($tabela_precos AS $tabela_preco)
                                    <option value="{{$tabela_preco->id}}">{{$tabela_preco->descr}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label for="empresa" class="custom-label-form">Empresa *</label>
                            <select id="empresa" name="empresa" class="custom-select"required>
                                @foreach($empresas AS $empresa)
                                    <option value="{{$empresa->id}}">{{$empresa->descr}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="valor" class="custom-label-form">Valor *</label>
                            <input id="valor" name="valor" class="form-control" autocomplete="off" type="number" step="0.01" required>
                        </div>
                        <div class="col-md-1 form-group d-grid">
                            <button class="btn btn-target mt-auto" type="button" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_plano_convenio()">
                                <i class="my-icon fas fa-plus"></i>
                            </button>
                        </div>
                        
                    </form>
                    <div class="col-12" style="padding:0">
                        <div class="custom-table card h-100">
                            <div class="table-header-scroll">
                                <table>
                                    <thead>
                                        <tr>
                                            <th width="50%">Plano</th>
                                            <th width="35%">Empresa</th>
                                            <th width="10%" class="text-left">Valor</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="table-body-scroll">
                                <table id="tabela_precos" class="table table-hover">
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="salvar_convenio();" class="btn btn-target my-3 mx-auto px-5"  style="display:flex">
                        Gravar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>