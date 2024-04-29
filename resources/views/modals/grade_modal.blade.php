<!-- Modal -->
<div class="modal fade" id="gradeModal" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="gradeModalLabel">Cadastrar Grade</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <form id="cadastro-grade-form" class="row" action="/saude-beta/grade/salvar" method="POST" onsubmit="verificar_grade_por_semana(event); return false;">
                        @csrf
                        <input id="id_profissional" name="id_profissional" type="hidden" required>

                        <div class="col-md-4">
                            <label for="dia-semana" class="custom-label-form">Dia da Semana*</label>
                            <select id="dia-semana" name="dia_semana" class="form-control custom-select">
                                <option value="1">Domingo</option>
                                <option value="2">Segunda</option>
                                <option value="3">Terça</option>
                                <option value="4">Quarta</option>
                                <option value="5">Quinta</option>
                                <option value="6">Sexta</option>
                                <option value="7">Sábado</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="empresa" class="custom-label-form">Empresa</label>
                            <select onchange="abrir_grades_pessoa($('#gradeModal #id_profissional').val())" id="empresa" name="empresa" class="form-control custom-select select*">
                                
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="etiqueta" class="custom-label-form">Etiqueta</label>
                            <select id="etiqueta" name="etiqueta" class="form-control custom-select select*">
                                <option value="">Selecionar Etiqueta...</option>
                                @if (isset($etiquetas))
                                @foreach ($etiquetas as $etiqueta)
                                    <option value="{{ $etiqueta->id }}">
                                        {{ $etiqueta->descr }}
                                        <div class="list-item-color"
                                            style="background-color: {{ $etiqueta->cor }}; border-color: {{ $etiqueta->cor }}"></div>
                                    </option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="hora_inicio" class="custom-label-form">Hora Ínicio*</label>
                            <input id="hora_inicio" name="hora_inicio" class="form-control timing" placeholder="__:__" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4">
                            <label for="hora_final" class="custom-label-form">Hora Fim*</label>
                            <input id="hora_final" name="hora_final" class="form-control timing" placeholder="__:__" autocomplete="off" type="text" required>
                        </div>

                        <div class="col-md-4">
                            <label for="min_intervalo" class="custom-label-form">Grade Intervalo (min)*</label>
                            <input id="min_intervalo" name="min_intervalo" class="form-control" autocomplete="off" type="number" value="30" required>
                        </div>

                        <div class="col-md-4">
                            <label for="max-qtde-pacientes" class="custom-label-form">Máx. Associados</label>
                            <input id="max-qtde-pacientes" name="max_qtde_pacientes" class="form-control" autocomplete="off" placeholder="Ilimitado" type="number">
                        </div>

                        <div class="col-md-4">
                            <label for="data_inicial" class="custom-label-form">Vigente desde</label>
                            <input id="data_inicial" name="data_inicial" class="form-control date" autocomplete="off" placeholder="Sempre" type="text">
                        </div>

                        <div class="col-md-4">
                            <label for="data_final" class="custom-label-form">até*</label>
                            <input id="data_final" name="data_final" class="form-control date" autocomplete="off" placeholder="Sempre" type="text">
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="obs" class="custom-label-form">Observações</label>
                            <textarea id="obs" name="obs" class="form-control" rows="2" maxlength="255"></textarea>
                        </div>

                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-target">
                                Salvar
                            </button>
                        </div>
                    </form>
                    <hr>
                    <h4>Grade do Membro</h4>
                    <div class="container grade-semana">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Domingo</h6>
                                <div data-dia_semana="1">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Segunda</h6>
                                <div data-dia_semana="2">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Terça</h6>
                                <div data-dia_semana="3">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Quarta</h6>
                                <div data-dia_semana="4">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Quinta</h6>
                                <div data-dia_semana="5">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Sexta</h6>
                                <div data-dia_semana="6">
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="text-center py-2 m-0">Sábado</h6>
                                <div data-dia_semana="7">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
