
<!-- Modal -->
<div class="modal fade" id="baixarTitulosPagarModal" aria-labelledby="baixarTitulosPagarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 45%;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="baixarTitulosPagarModalLabel">Baixar Título</h6>
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
                        </div>
                    </div>
                </div>

                <div class="d-flex">
                    <button onclick="salvarBaixaPagar()" type="button" class="btn btn-target mx-auto my-3 px-5">
                        Baixar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
