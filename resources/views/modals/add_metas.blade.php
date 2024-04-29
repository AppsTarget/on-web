<!-- Modal -->
<div class="modal fade" id="metasModal" aria-labelledby="metasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="metasModalLabel">Cadastrar Preços Por Procedimento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        @csrf
                        <input id="id-modalidade" name="id-modalidade" type="hidden">

                        <div class="col-12">
                            <h4 class="m-0">Comissões Especiais</h4>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="tipo_de_comissao" class="custom-label-form">Tipo de comissão</label>
                            <select id="tipo_de_comissao" class="custom-select">
                                <option value="%">Percentual (%)</option>
                                <option value="F">Valor Fixo</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="modalidade" class="custom-label-form">procedimento</label>
                            <select id="modalidade" class="custom-select" name="modalidade" disabled="true">
                                @foreach($procedimentos as $procedimento)
                                    <option value="{{$procedimento->id}}">{{$procedimento->descr}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="filtro-tabela-precos" class="row w-100 m-0">
                            <div class="col-md-2 form-group">
                                <label for="de" class="custom-label-form">De:</label>
                                <input id="de" class="form-control" name="de" type="number" step="1" disabled="true" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="ate" class="custom-label-form">Ate:</label>
                                <input id="ate" class="form-control" name="ate" type="number" step="1">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="valor" class="custom-label-form">Valor</label>
                                <input id="valor" class="form-control" name="valor" type="number" step="0.1">
                            </div>

                            <div class="col-1 form-group d-grid" style="padding: 0;margin-right: 8%;">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_meta_modalidade()">
                                    <svg class="svg-inline--fa fa-plus fa-w-14 my-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path></svg>
                                </button>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="acima-de" class="custom-label-form">Acima de:</label>
                                <input id="acima-de" class="form-control" name="acima-de" type="number" step="1" disabled="true">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="teto" class="custom-label-form">Valor:</label>
                                <input id="teto" class="form-control" name="teto" type="number" step="1">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="custom-table card h-100">
                                <div class="table-header-scroll">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="35%">De</th>
                                                <th width="35%">Até</th>
                                                <th width="20%" class="text-left">Valor</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="table-body-scroll">
                                    <table id="table-metas" class="table table-hover">
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex col-12">
                            <button type="button" class="btn btn-target mx-auto my-3 px-5" onclick="salvar_metas_modalidade();">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
