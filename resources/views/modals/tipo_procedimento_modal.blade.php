
<!-- Modal -->
<div class="modal fade" id="tipoprocedimentoModal" aria-labelledby="tipoprocedimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="tipoprocedimentoModalLabel">Cadastrar Tipo de Agendamento</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form >
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            @csrf
                            <input id="id" name="id" type="hidden">
                            <div class="col-12">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="tempo-procedimento" class="custom-label-form">Tempo de procedimento (Min.)</label>
                                <input id="tempo-procedimento" name="tempo_procedimento" class="form-control" autocomplete="off" type="number">
                            </div>

                            <div class="custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;'>
                                <input onclick='libera_especialidade()' id="assossiar_especialidade" name="assossiar_especialidade" class="custom-control-input" type="checkbox">
                                <label for="assossiar_especialidade" class="custom-control-label" >
                                    <span style='color: #6c6c6c;'>Realizar venda ao faturar agendamento?</span>
                                </label>
                            </div>

                            <div class="custom-control custom-checkbox custom-control-inline mr-2" style='left: 15px;'>
                                <input onclick='libera_plano()' id="assossiar_contrato" name="assossiar_contrato" class="custom-control-input" type="checkbox" >
                                <label for="assossiar_contrato" class="custom-control-label">
                                    <span style='color: #6c6c6c;'>Assossiar tipo de Agendamento com Contrato?</span>
                                </label>
                            </div>

                            {{-- <div class="col-md-12 form-group">
                                <label for="tabela_preco" class="custom-label-form">Área da saúde *</label>
                                <select id="tabela_preco" name="tabela_preco" class="form-control custom-select" disabled="true">
                                    <option value="">
                                        Selecionar plano...
                                    </option>
                                    @foreach ($tabela_precos as $plano)
                                    <option value="{{ $plano->id }}">
                                        {{ $plano->descr }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                    </div>
                </div>
                
                <div class="d-flex">
                    <button type="button" onclick='criar_tipo_agendamento()' class="btn btn-target mx-auto my-3 px-5">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function libera_especialidade() {
        if ($("#assossiar_especialidade").prop('checked') == false){
            $("#especialidade").prop('disabled', true);
        }
        else $("#especialidade").prop('disabled', false);
    }
    function libera_plano() {
        if ($("#assossiar_tabela_preco").prop('checked') == false){
            $("#tabela_preco").prop('disabled', true);
        }
        else $("#tabela_preco").prop('disabled', false);
    }
</script>