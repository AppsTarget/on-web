<!-- Modal -->
<div class="modal fade" id="agendamentosEmLoteModal" aria-labelledby="agendamentosEmLoteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:620px">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="agendamentosEmLoteModalLabel">
                    <span id = "agendamentosEmLoteModalLabelS">Criar Agendamento</span>
                    <small class="invalid-feedback"></small>
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form id="criar-agendamento-form" class="row" action="/saude-beta/agenda/salvar" method="POST">
                        @csrf
                        <input id="id-grade-horario" name="id_grade_horario" type="hidden" required>
                        <input id="id-profissional" name="id_profissional" type="hidden" required>
                        <input id="id" name="id" type="hidden" required>

                        <div class="col-12 form-search">
                            <label for="paciente_nome" class="custom-label-form">Associado *</label>   
                            <input  id="paciente_nome"
                                    name="paciente_nome"  
                                    class="form-control autocomplete" 
                                    placeholder="Digitar nome do associado..."
                                    data-input="#agendamentosEmLoteModal #paciente_id"
                                    data-table="pessoa" 
                                    data-column="nome_fantasia" 
                                    data-filter_col="paciente"
                                    data-filter="S"
                                    type="text" 
                                    autocomplete="off"
                                    
                                    {{-- onchange="mod=0;if(isLote&&document.getElementById('paciente_id').value!='')modAnt=document.getElementById('paciente_id').value;control_criar_agendamento_lote(()=>{console.log('a')});" --}}
                                    {{-- onfocusout="$('#agendamentosEmLoteModal #id_contrato').val(0)" --}}
                                    {{-- onmouseout="control_criar_agendamento_lote()" --}}
                                    required
                            >
                            <input id="paciente_id" name="paciente_id" type="hidden" onchange="encontrarContratosLote()" >
                        </div>

                        

                        <div class="col-md-12 form-group agendamentoLote" id="contratos" style="display: block;min-width: 50%;">
                            <label for="id_contrato" class="custom-label-form">Contratos</label>
                            <select id="id_contrato" onblur="encontrarPlanosContratoLote($(this).val());" name="id_contrato" class="custom-select">
                                <option value="0">Selecionar contrato...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 form-group agendamentoLote" id="planos_por_contrato" style="display: block; min-width: 50%;">
                            <label for="id_plano" class="custom-label-form">Planos</label>
                            <select id="id_plano" name="id_plano" class="custom-select">
                                <option value="0" disabled>Selecionar plano...</option>
                            </select>
                        </div>

                        
                        <div id="agendamento-footer" class="row col-12 pr-0">
                            <div class="col-md-12 pr-0 text-center">
                                <button type="button" onclick="abrirAgendamentoLoteModal($('#agendamentosEmLoteModal #paciente_id').val(),$('#agendamentosEmLoteModal #id_contrato').val(),$('#agendamentosEmLoteModal #id_plano').val())" class="btn btn-target px-5" id="continuar" value="">Continuar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style type = "text/css">
#criar-agendamento-form>div{margin-bottom:20px}
</style>