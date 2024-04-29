<!-- Modal -->
<div class="modal fade" id="cadastroCaixaModal" aria-labelledby="cadastroCaixaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="cadastroCaixaModalLabel">Cadastro de Caixa</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <input id="id_caixa" type="hidden">
                            <div class="col-md-12 form-group">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="empresa" class="custom-label-form">Empresa</label>
                                <select id="empresa" name="empresa" class="custom-select">
                                    @foreach ($empresa as $emp)
                                        <option value="{{$emp->id}}">{{$emp->descr}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="situacao" class="custom-label-form">Situação</label>
                                <select id="situacao" name="situacao" class="custom-select">
                                    <option value="S">Ativo</option>
                                    <option value="N">Inativo</option>
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label for="horainicial" class="custom-label-form">Hora Inicial*</label>
                                <input id="horainicial" name="horainicial" class="form-control timing" autocomplete="off" type="text" placeholder="__:__" required>
                            </div>
                            <div class="col-6 form-group">
                                <label for="horafinal" class="custom-label-form">Hora Final*</label>
                                <input id="horafinal" name="horafinal" class="form-control timing" autocomplete="off" type="text" placeholder="__:__" required>
                            </div>
                            <div class="col-10 form-group">
                                <label for="paciente_nome" class="custom-label-form">Associado *</label>   
                                <input id="caixa_consultor_profissional_descr"
                                    name="caixa_consultor_profissional_descr"
                                    class="form-control autocomplete"
                                    placeholder="Digitar Nome do Consultor..."
                                    data-input="#caixa_consultor_profissional_id"
                                    data-table="pessoa"
                                    data-column="nome_fantasia"
                                    data-filter_col="colaborador"
                                    data-filter="R"
                                    type="text"
                                    autocomplete="off">
                                <input id="caixa_consultor_profissional_id" name="caixa_consultor_profissional_id" type="hidden">
                            </div>
                            
                            <div class="col-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_consultor_caixa()" type="button">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div> 
                        </div>
                        <div class="table-header-scroll">
                            <table>
                                <thead>
                                    <th>
                                        Consultor
                                    </th>
                                    <th>
                                    
                                    </th>
                                </thead>
                            </table>
                        </div>
                        <div class="table-body-scroll">
                            <table id="table-cadastro-caixa">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button onclick="salvarCadastroCaixa()" type="button" class="btn btn-target m-auto px-5">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
