<!-- Modal -->
<div class="modal fade" id="procedimentoModal" aria-labelledby="procedimentoModalLabel" aria-hidden="true" style="user-select:none">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="procedimentoModalLabel">Cadastrar modalidades</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form class="row" action="/saude-beta/procedimento/salvar" method="POST" onsubmit="salvar_procedimento(event)">
                        @csrf
                        <input id="id" name="id" type="hidden">

                        <div class="col-md-12 form-group">
                            <label for="especialidade" class="custom-label-form">Área da saúde *</label>
                            <select id="especialidade" name="especialidade" class="form-control custom-select">
                                <option value="">
                                    Selecionar área da saúde...
                                </option>
                                @foreach ($especialidades as $especialidade)
                                <option value="{{ $especialidade->id }}">
                                    {{ $especialidade->descr }}
                                </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-12 form-group">
                            <label for="descr" class="custom-label-form">Descrição *</label>
                            <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="descr-resumida" class="custom-label-form">Descrição Resumida</label>
                            <input id="descr-resumida" name="descr_resumida" class="form-control" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-12 form-group" style="text-align:right">
                            <label for="faturar" class="custom-label-form">Gerar Faturamento</label>
                            <input id="faturar" type="checkbox" onchange="$('#faturar_val').val($(this).is(':checked')?1:0)" />
                        </div>
                        <input type="hidden" id="faturar_val" name="faturar" value="0"/>

                        <div class="col-md-12 form-group" style="margin-top:-10px">
                            <label for="obs" class="m-0 mt-2">Observações</label>
                            <textarea id="obs" name="obs" class="form-control" rows="4"></textarea>
                        </div>

                        <button type="submit" class="btn btn-target my-3 mx-auto px-5">
                            Gravar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
