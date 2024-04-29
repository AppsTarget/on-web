<!-- Modal -->
<div class="modal fade" id="criarReceitaModal" aria-labelledby="criarReceitaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarReceitaModalLabel">Cadastrar Receita</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/saude-beta/receita/salvar" method="POST" onsubmit="salvar_receita(event)">
                    @csrf
                    <input id="id_paciente" name="id_paciente" value="{{ $pessoa->id }}" type="hidden">
            
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-10 form-group form-search">
                                <label for="medicamento_nome" class="custom-label-form">Medicamento *</label>   
                                <input id="medicamento_nome"
                                    name="medicamento_nome"  
                                    class="form-control autocomplete" 
                                    style="text-transform:uppercase"
                                    placeholder="Digitar Nome do Medicamento..."
                                    data-input="#medicamento_id"
                                    data-table="medicamento" 
                                    data-column="descr" 
                                    data-filter_col="ativo"
                                    data-filter="1"
                                    type="text" 
                                    autocomplete="off"
                                    required>
                                <input id="medicamento_id" name="medicamento_id" type="hidden" required>
                            </div>

                            <div class="col-md-1 form-group d-grid">
                                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_receita_medicamento(); return false">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="lista-receita-medicamentos" class="container-fluid card">
                    </div>

                    <div class="col-12 d-grid">
                        <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>