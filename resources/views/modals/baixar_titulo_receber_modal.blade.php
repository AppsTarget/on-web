
<!-- Modal -->
<div class="modal fade" id="baixarTitulosReceberModal" aria-labelledby="baixarTitulosReceberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 45%;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="baixarTitulosReceberModalLabel">Baixar título</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/saude-beta/agenda-status/salvar" method="POST">
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <input id="id-titulo-baixar" type="hidden">
                            <div class="col-6 form-group">
                                <label for="valor-total" class="custom-label-form">Valor *</label>
                                <input id="valor-total" class="form-control text-right money-brl" placeholder="R$ 0,00" type="text" min="1">
                            </div>
                            <div class="col-6 form-group">
                                <label for="conta" class="custom-label-form">Conta *</label>
                                <select id="conta" class="custom-select">
                                    @foreach ($contas As $conta)
                                        <option value="{{ $conta->id }}">{{ $conta->titular }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label for="data-baixa" class="custom-label-form">Data *</label>
                                <input id="data-baixa" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
                            </div>
                            <div class="col-6 form-group">
                                <label for="forma_pag" class="custom-label-form">Forma_pag *</label>
                                <select id="forma_pag" class="custom-select">
                                    @foreach ($formas_pag As $forma_pag)
                                        <option value="{{ $forma_pag->id }}">{{ $forma_pag->descr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 form-group">
                                <label for="desconto" class="custom-label-form">Desconto *</label>
                                <input id="desconto" class="form-control text-right money-brl" placeholder="R$ 0,00" type="text" min="1">
                            </div>
                            <div class="col-6 form-group">
                                <label for="acrescimo" class="custom-label-form">Acréscimo *</label>
                                <input id="acrescimo" class="form-control text-right money-brl" placeholder="R$ 0,00" type="text" min="1">
                            </div>
                            <div class="col-10 form-group">
                                <label for="historico" class="custom-label-form">Histórico *</label>
                                <input id="historico" class="form-control" type="text" disabled>
                                <input id="historico-id" clas="form-control" type="hidden">
                            </div>
                            <div class="col-2 form-group d-flex">
                                <button type="button" class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px); width: 100%" onclick="addPlanoContaReceberModal()">
                                    <svg class="svg-inline--fa fa-plus fa-w-14 my-icon" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex">
                    <button onclick="salvarBaixaReceber()" type="button" class="btn btn-target mx-auto my-3 px-5">
                        Baixar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
