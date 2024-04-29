<!-- Modal -->
<div class="modal fade" id="agendamentoLoteModal2" aria-labelledby="agendamentoLoteModal2" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form>
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color">Criar agendamento em lote - Passo 3/3 (Sessão <span id = "numAg"></span>/<span id = "numAgTotal"></span>)</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding-bottom:0">
                    <h4 id='CRlote2'class='text-center'></h4>
                    <h4 id="ASlote2" class='text-center'></h4>
                    <h6 id="SEMlote2" class='text-center' style="margin-bottom:30px"></h6>
                    <div class = "col12" style = "
                        height: 26px;
                        border: solid 1px #ced4da;
                        margin-top: -13px;
                        border-radius: 9px;
                        margin-bottom: 13px;
                        overflow:hidden
                    ">
                        <div id = "porc" style = "
                            background:#0067d533;
                            height: 100%;
                        "></div>
                    </div>
                    <table style = "margin-bottom:30px">
                        <tr>
                            <td style = "width:50%">
                                <div class="col-12 form-search">
                                    <label for="loteModalidade" class="custom-label-form">Modalidade *</label>
                                    <select id="loteModalidade" name="loteModalidade" class="custom-select">
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="col-12 form-search">
                                    <label for="prof_nome" class="custom-label-form">Profissional *</label>
                                    <input  id="prof_nome"
                                            name="prof_nome"  
                                            class="form-control autocomplete" 
                                            placeholder="Digitar nome do profissional..."
                                            data-input="#prof_id"
                                            data-table="pessoa(membro)" 
                                            data-column="nome_fantasia" 
                                            data-filter_col=""
                                            data-filter=""
                                            type="text" 
                                            autocomplete="off"
                                            onchange="empresasPorProfissional()"
                                            required
                                    >
                                    <input id="prof_id" name="prof_id" type="hidden">
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="col-12 form-search" style = "margin-bottom:30px">
                        <label for="loteEmpresa" class="custom-label-form">Empresa *</label>
                        <select id="loteEmpresa" name="loteEmpresa" onchange="datasPorEmpresa(this.value)" class="custom-select">
                            <option value = "0" disabled selected>Selecione a empresa...</option>
                        </select>
                    </div>

                    <table style = "width:100%;margin-bottom:3rem">
                        <tr>
                            <td style = "width:50%">
                                <div class="col-12 form-search">
                                    <label for="loteData" class="custom-label-form">Dia da semana *</label>
                                    <select id="loteData" name="loteData" onchange="horasPorData(this.value)" class="custom-select">
                                        <option value = "0" disabled selected>Selecione o dia da semana...</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="col-12 form-search">
                                    <label for="loteHora" class="custom-label-form">Horário *</label>
                                    <select id="loteHora" name="loteHora" onchange="this.style.borderColor=''" class="custom-select">
                                        <option value = "0" disabled selected>Selecione o horário...</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>
                <div class="container" style="margin-bottom:30px;width:56%">
                    <div class="row my-3">
                        <button class="btn btn-target m-auto px-5" type="button" onclick="agendamentoLoteCriaSessao(true, true)">Concluir</button>
                        <button id="loteContinuar" class="btn btn-target m-auto px-5" type="button" onclick="agendamentoLoteCriaSessao(false, true)">Continuar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
#agendamentoLoteModal2 ::marker{
    content:"►"
}
#agendamentoLoteModal2 details[open] > ::marker{
    content:"▼"
}
#agendamentoLoteModal2 details {
    margin-bottom:20px
}
</style>