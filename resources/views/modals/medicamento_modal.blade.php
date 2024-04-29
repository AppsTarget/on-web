<!-- Modal -->
<div class="modal fade" id="criarMedicamentoModal" aria-labelledby="criarMedicamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarMedicamentoModalLabel">Cadastrar Medicamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="row" action="/saude-beta/medicamento/salvar" method="POST">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="col-9">
                            <label for="descr" class="custom-label-form">Descrição</label>
                            <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-3">
                            <label for="ativo" class="custom-label-form">Ativo</label>
                            <select id="ativo" name="ativo" class="custom-select">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="uso" class="custom-label-form">Uso</label>
                            <select id="uso" name="uso" class="custom-select">
                                <option value="ORAL">ORAL</option>
                                <option value="INTERNO">INTERNO</option>
                                <option value="EXTERNO">EXTERNO</option>
                                <option value="INTRA-NASAL">INTRA-NASAL</option>
                            </select>
                        </div>
                
                        <div class="col-6">
                            <label for="tipo" class="custom-label-form">Tipo</label>
                            <select id="tipo" name="tipo" class="custom-select">
                                <option value="COMPRIMIDO">COMPRIMIDO</option>
                                <option value="FRASCO">FRASCO</option>
                                <option value="AEROSSOL">AEROSSOL</option>
                                <option value="CAPSULA">CAPSULA</option>
                                <option value="COLIRIO">COLIRIO</option>
                                <option value="ELIXIR">ELIXIR</option>
                                <option value="EMULSAO">EMULSAO</option>
                                <option value="ENEMA">ENEMA</option>
                                <option value="INJETAVEL">INJETAVEL</option>
                                <option value="OVULO">OVULO</option>
                                <option value="PASTULHA">PASTULHA</option>
                                <option value="PILULA">PILULA</option>
                                <option value="POCAO">POCAO</option>
                                <option value="POMADA">POMADA</option>
                                <option value="SUPOSITORIO">SUPOSITORIO</option>
                                <option value="TERIAGA">TERIAGA</option>
                                <option value="XAROPE">XAROPE</option>
                            </select>
                        </div>
                
                        {{-- <div class="col-2">
                            <label for="unidade" class="custom-label-form">Unidade</label>
                            <select id="unidade" name="unidade" class="custom-select">
                                <option value="UN">UN</option>
                                <option value="KG">KG</option>
                                <option value="MT">MT</option>
                                <option value="PC">PC</option>
                                <option value="CX">CX</option>
                                <option value="ML">ML</option>
                                <option value="CAP">CAP</option>
                                <option value="FRA">FRA</option>
                            </select>
                        </div> --}}

                
                        <div class="col-12">
                            <label for="posologia" class="custom-label-form">Posologia</label>
                            <textarea id="posologia" name="posologia" class="form-control" rows="4" required></textarea>
                        </div>
                
                        <div class="col-12 d-grid">
                            <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>