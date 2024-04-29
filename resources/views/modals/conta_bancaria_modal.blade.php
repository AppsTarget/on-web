<!-- Modal -->
<div class="modal fade" id="contaBancariaModal" aria-labelledby="contaBancariaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form action="/saude-beta/forma-pag/salvar" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="contaBancariaModalLabel">Contas Bancárias</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id="id" type="hidden">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="titular" class="custom-label-form">Titular *</label>
                                <input id="titular" name="titular" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="conta" class="custom-label-form">Conta *</label>
                                <input id="conta" name="conta" class="form-control" data-mask="0000-0" data-mask-reverse="true" autocomplete="off" type="text" maxlength="14">  
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="agencia" class="custom-label-form">Agencia *</label>
                                <input id="agencia" name="agencia" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-6 form-group form-search">
                                <label for="banco_descr" class="custom-label-form">
                                    Banco
                                </label>
                                <input id="banco_descr"
                                    name="banco_descr"
                                    class="form-control autocomplete"
                                    placeholder="Busque o Nome do Banco..."
                                    data-input="#banco_id"
                                    data-table="bancos"
                                    data-column="title"
                                    data-filter_col=""
                                    data-filter=""
                                    type="text"
                                    autocomplete="off"
                                    required>
                                <input id="banco_id" name="banco_id" type="hidden">
                            </div>
                            <div class="col form-group form-search">
                                <label for="empresa" class="custom-label-form">
                                    Empresa
                                </label>
                                <select id="empresa" class="custom-select">
                                    @foreach($empresa AS $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->descr }}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="col-4 form-group form-search" style="display:none">
                                <label for="caixa" class="custom-label-form">
                                    Caixa
                                </label>
                                <select id="caixa" class="custom-select">
                                    @foreach($caixas AS $caixa)
                                        <option value="{{ $caixa->id }}">{{ $caixa->descr }}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="col-md-12 form-group" style="display: flex;justify-content: space-between;">
                                <div class="custom-control custom-switch">
                                    <input id="conta_conrrente" name="conta_conrrente" class="custom-control-input" type="checkbox">
                                    <label for="conta_conrrente" class="custom-control-label" style="width:175px">Conta Corrente</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input id="conta_poupanca" name="conta_poupanca" class="custom-control-input" type="checkbox">
                                    <label for="conta_poupanca" class="custom-control-label" style="width:175px">Conta Poupança</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input id="conta_cofre" onchange="controlContaCofre($(this))" name="conta_cofre" class="custom-control-input" type="checkbox">
                                    <label for="conta_cofre" class="custom-control-label" style="width:175px">Conta Cofre</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input id="conta_caixa" onchange="controlContaCaixa($(this))" name="conta_caixa" class="custom-control-input" type="checkbox">
                                    <label for="conta_caixa" class="custom-control-label" style="width:175px">Conta Caixa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button onclick="salvarContabancaria()" class="btn btn-target m-auto px-5" type="button">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
